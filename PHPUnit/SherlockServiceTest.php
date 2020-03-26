<?php

namespace App\Tests\V3\Services\Sherlock;

use App\Tests\AbstractTestCaseForUnit;
use App\V3\Services\Sherlock\Constructors\SherlockResponseConstructor;
use App\V3\Services\Sherlock\Constructors\SherlockResponseTypeChildrenConstructor;
use App\V3\Services\Sherlock\Constructors\SherlockResponseTypeLogicConstructor;
use App\V3\Services\Sherlock\Helpers\SherlockTemplates;
use App\V3\Services\Sherlock\SherlockLogic\AbstractSherlockLogic;
use App\V3\Services\Sherlock\SherlockLogic\DefaultLogic\SearchNothingLogic;
use App\V3\Services\Sherlock\SherlockResponse\DefaultSherlockResponse;
use App\V3\Services\Sherlock\SherlockResponse\ResponseFormatter\DefaultFormatter;
use App\V3\Services\Sherlock\SherlockResponse\SherlockResponseType\CompositeSherlockResponseType;
use App\V3\Services\Sherlock\SherlockResponse\SherlockResponseType\SherlockResponseTypeLogic;
use App\V3\Services\Sherlock\SherlockService;
use App\V3\Services\Sherlock\SherlockSimpleRequest;

class SherlockServiceTest extends AbstractTestCaseForUnit
{

    public function testSimpleResponse() {
        $sherlockService = new SherlockService();

        $nothingLogic = new SearchNothingLogic();

        $sherlockService->setLogic(SherlockTemplates::TEMPLATE_NOTHING, $nothingLogic);

        $sherlockRequest = new SherlockSimpleRequest(['foo' => 'bar']);

        $sherlockResponse = new DefaultSherlockResponse($sherlockService, SherlockTemplates::TEMPLATE_NOTHING, $sherlockRequest);

        $sherlockResponse->setOptions(['advance' => 'More, more, more...']);

        $result = $sherlockResponse->getResult();

        $resultString = json_encode($result);

        $simpleTestControlString = '{"request_body":{"foo":"bar"},"options":{"advance":"More, more, more..."},"our_info":"Smiles to you grandfather Makar"}';

        $this->assertJsonStringEqualsJsonString($resultString, $simpleTestControlString);
    }

    public function testSimpleResponseWithChildren() {
        $sherlockService = new SherlockService();

        $nothingLogic = new SearchNothingLogic();

        $anotherLogic = $this->createAnotherLogic($sherlockService);

        $sherlockService->setLogic('templ1', $nothingLogic)
            ->setLogic('templ2', $anotherLogic);

        $sherlockRequest = new SherlockSimpleRequest(['foo' => 'bar']);

        $sherlockResponseFirst = new DefaultSherlockResponse($sherlockService, 'templ1', $sherlockRequest);
        $sherlockResponseSecond = new DefaultSherlockResponse($sherlockService, 'templ2', $sherlockRequest);
        $sherlockResponseSecond->setOptions(['foo' => 'bar']);
        $sherlockResponseThird = new DefaultSherlockResponse($sherlockService, 'templ2', $sherlockRequest);

        $sherlockResponseWithChildren = new DefaultSherlockResponse(
            $sherlockService,
            CompositeSherlockResponseType::class,
            $sherlockRequest
        );
        $sherlockResponseWithChildren->setOptions(['this is a good day to die']);
        $sherlockResponseWithChildren->getType()->addChildren('cities', $sherlockResponseFirst);
        $sherlockResponseWithChildren->getType()->addChildren('regions', $sherlockResponseSecond);

        $sherlockResponseWithChildrenSecond = new DefaultSherlockResponse($sherlockService, CompositeSherlockResponseType::class, $sherlockRequest);
        $sherlockResponseWithChildrenSecond->getType()->addChildren('other', $sherlockResponseWithChildren);
        $sherlockResponseWithChildrenSecond->getType()->addChildren('current', $sherlockResponseThird);
        $sherlockResponseWithChildrenSecond->setOptions(['bar' => 'blue Oyster']);

        $result = $sherlockResponseWithChildrenSecond->getResult();

        $resultString = json_encode($result);

        $simpleTestControlString = '{"other":{"cities":{"request_body":{"foo":"bar"},"options":[],"our_info":"Smiles to you grandfather Makar"},"regions":{"another_logic_response":"i am"}},"current":{"another_logic_response":"i am"}}';

        $this->assertJsonStringEqualsJsonString($resultString, $simpleTestControlString);
    }

    public function testConstructorSimpleResponse() {
        $data = [
            SherlockResponseConstructor::FIELD_FORMATTER => DefaultFormatter::class,
            SherlockResponseConstructor::FIELD_LOGIC_NAME => 'test_name_1',
            SherlockResponseConstructor::FIELD_OPTIONS => ['bar' => 'blue Oyster']
        ];

        $sherlockService = new SherlockService();

        $nothingLogic = new SearchNothingLogic();
        $sherlockService->setLogic('test_name_1', $nothingLogic);

        $sherlockRequest = new SherlockSimpleRequest(['foo' => 'bar']);

        $sherlockLogicConstructor = new SherlockResponseTypeLogicConstructor();
        $response = $sherlockLogicConstructor->create($sherlockService, $data, $sherlockRequest);

        $result = $response->getResult();
        $resultString = json_encode($result);

        $simpleTestControlString = '{"request_body":{"foo":"bar"},"options":{"bar":"blue Oyster"},"our_info":"Smiles to you grandfather Makar"}';

        $this->assertJsonStringEqualsJsonString($resultString, $simpleTestControlString);
    }

    public function testConstructorChildrenResponse() {
        $sherlockService = new SherlockService();

        $nothingLogic = new SearchNothingLogic();
        $anotherLogic = $this->createAnotherLogic($sherlockService);

        $sherlockService->setLogic('test_name_1', $nothingLogic)
            ->setLogic('test_name_2', $anotherLogic);

        $defaultFormatter = new DefaultFormatter();

        $data = [
            'cities' => [
                [
                    SherlockResponseConstructor::FIELD_FORMATTER => $defaultFormatter,
                    SherlockResponseConstructor::FIELD_LOGIC_NAME => 'test_name_1',
                    SherlockResponseConstructor::FIELD_OPTIONS => ['3' => '1']
                ],
                [
                    [
                        SherlockResponseConstructor::FIELD_LOGIC_NAME => 'test_name_2',
                        SherlockResponseConstructor::FIELD_OPTIONS => ['000' => '0', '00', 0]
                    ],
                    [
                        SherlockResponseConstructor::FIELD_LOGIC_NAME => 'test_name_1',
                        SherlockResponseConstructor::FIELD_OPTIONS => [1, 2, 4]
                    ]
                ],
                [
                    SherlockResponseConstructor::FIELD_LOGIC_NAME => 'test_name_1',
                    SherlockResponseConstructor::FIELD_OPTIONS => ['3' => '1'],
                    'foo' => 'who',
                    'no_thanks'
                ]
            ],
            'other' => [
                [
                    SherlockResponseConstructor::FIELD_FORMATTER => $defaultFormatter,
                    SherlockResponseConstructor::FIELD_LOGIC_NAME => 'test_name_2',
                    SherlockResponseConstructor::FIELD_OPTIONS => ['no' => 'yes']
                ],
                [
                    SherlockResponseConstructor::FIELD_FORMATTER => $defaultFormatter,
                    SherlockResponseConstructor::FIELD_LOGIC_NAME => 'test_name_1',
                    SherlockResponseConstructor::FIELD_OPTIONS => [['who'=>'you'], 'is'=>'he']
                ]
            ],
            'region' => [
                SherlockResponseConstructor::FIELD_FORMATTER => DefaultFormatter::class,
                SherlockResponseConstructor::FIELD_LOGIC_NAME => 'test_name_1',
                SherlockResponseConstructor::FIELD_OPTIONS => ['bar' => 'blue Oyster']
            ]
        ];

        $sherlockLogicConstructor = new SherlockResponseTypeLogicConstructor();
        $sherlockChildrenConstructor = new SherlockResponseTypeChildrenConstructor($sherlockLogicConstructor);

        $sherlockRequest = new SherlockSimpleRequest(['foo' => 'bar']);
        $response = $sherlockChildrenConstructor->create($sherlockService, $data, $sherlockRequest);

        $result = $response->getResult();
        $resultString = json_encode($result);

        $simpleTestControlString = '{"cities":[{"request_body":{"foo":"bar"},"options":["1"],"our_info":"Smiles to you grandfather Makar"},[{"another_logic_response":"i am"},{"request_body":{"foo":"bar"},"options":[1,2,4],"our_info":"Smiles to you grandfather Makar"}],{"request_body":{"foo":"bar"},"options":{"0":"1","foo":"who","1":"no_thanks"},"our_info":"Smiles to you grandfather Makar"}],"other":[{"another_logic_response":"i am"},{"request_body":{"foo":"bar"},"options":{"0":{"who":"you"},"is":"he"},"our_info":"Smiles to you grandfather Makar"}],"region":{"request_body":{"foo":"bar"},"options":{"bar":"blue Oyster"},"our_info":"Smiles to you grandfather Makar"}}';

        $this->assertEquals($resultString, $simpleTestControlString);
    }

    protected function createAnotherLogic(SherlockService $sherlockService) {
        $anotherLogic = $this->createMock(AbstractSherlockLogic::class);

        // Настроить заглушку.
        $anotherLogic->method('setService')
            ->willReturn($anotherLogic);

        $anotherLogic->method('getService')
            ->willReturn($sherlockService);

        $anotherLogic->method('seek')
            ->willReturn(['another_logic_response' => 'i am']);

        return $anotherLogic;
    }
}
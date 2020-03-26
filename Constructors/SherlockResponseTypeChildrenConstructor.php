<?php

namespace App\V3\Services\Sherlock\Constructors;

use App\V3\Services\Sherlock\Exception\SherlockResponseConstructorException;
use App\V3\Services\Sherlock\SherlockRequest;
use App\V3\Services\Sherlock\SherlockResponse\AbstractSherlockResponse;
use App\V3\Services\Sherlock\SherlockResponse\DefaultSherlockResponse;
use App\V3\Services\Sherlock\SherlockResponse\ResponseFormatter\ResponseFormatter;
use App\V3\Services\Sherlock\SherlockResponse\SherlockResponseType\CompositeSherlockResponseType;
use App\V3\Services\Sherlock\SherlockService;

class SherlockResponseTypeChildrenConstructor implements SherlockResponseConstructor
{

    protected $logicConstructor;

    public function __construct(SherlockResponseConstructor $constructor)
    {
        $this->logicConstructor = $constructor;
    }

    /**
     * @param SherlockService $sherlockService
     * @param array $data
     * @param SherlockRequest $sherlockRequest
     * @return AbstractSherlockResponse
     * @throws SherlockResponseConstructorException
     */
    public function create(SherlockService $sherlockService, array $data,  SherlockRequest $sherlockRequest): AbstractSherlockResponse
    {

        if (isset($data[SherlockResponseConstructor::FIELD_FORMATTER])) {
            $formatter = $data[SherlockResponseConstructor::FIELD_FORMATTER];
            unset($data[SherlockResponseConstructor::FIELD_FORMATTER]);
            if ($formatter instanceof ResponseFormatter) {
                $formatterObject = $formatter;
            } elseif (is_string($formatter)) {
                $formatterObject = new $formatter;
            }
            else {
                throw new SherlockResponseConstructorException("Formatter type {$formatter} incorrect");
            }
        }

        $childrenResponse = new DefaultSherlockResponse($sherlockService, CompositeSherlockResponseType::class, $sherlockRequest);
        foreach($data as $childName => $subData) {
            $sherlockResponse = isset($subData[SherlockResponseTypeLogicConstructor::FIELD_LOGIC_NAME])
                ? $this->logicConstructor->create($sherlockService, $subData, $sherlockRequest)
                : $this->create($sherlockService, $subData, $sherlockRequest);
            $childrenResponse->getType()->addChildren($childName, $sherlockResponse);
        }

        if (!empty($formatterObject)) {
            $childrenResponse->setFormatter($formatterObject);
        }

        return $childrenResponse;
    }

}
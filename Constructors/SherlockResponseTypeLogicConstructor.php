<?php

namespace App\V3\Services\Sherlock\Constructors;

use App\V3\Services\Sherlock\Exception\SherlockResponseConstructorException;
use App\V3\Services\Sherlock\SherlockRequest;
use App\V3\Services\Sherlock\SherlockResponse\AbstractSherlockResponse;
use App\V3\Services\Sherlock\SherlockResponse\DefaultSherlockResponse;
use App\V3\Services\Sherlock\SherlockResponse\ResponseFormatter\ResponseFormatter;
use App\V3\Services\Sherlock\SherlockResponse\SherlockResponseType\SherlockResponseTypeLogic;
use App\V3\Services\Sherlock\SherlockService;

class SherlockResponseTypeLogicConstructor implements SherlockResponseConstructor
{

    /**
     * @param SherlockService $sherlockService
     * @param array $data
     * @param SherlockRequest $sherlockRequest
     * @return AbstractSherlockResponse
     * @throws SherlockResponseConstructorException
     */
    public function create(SherlockService $sherlockService, array $data, SherlockRequest $sherlockRequest): AbstractSherlockResponse
    {
        if (empty($data[SherlockResponseConstructor::FIELD_LOGIC_NAME])) {
            $logicNameField = SherlockResponseConstructor::FIELD_LOGIC_NAME;
            throw new SherlockResponseConstructorException("Field {$logicNameField} is required");
        }
        $logicName = $data[SherlockResponseConstructor::FIELD_LOGIC_NAME];

        if (!$sherlockService->hasLogic($logicName)) {
            $message = "Logic with name {$logicName} not registered into current SherlockService";
            throw new SherlockResponseConstructorException($message);
        }

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

        $options = !empty($data[SherlockResponseConstructor::FIELD_OPTIONS])
            ? $data[SherlockResponseConstructor::FIELD_OPTIONS]
            : [];

        //спорное решение. но я подумал: а почему бы и нет?!
        unset($data[SherlockResponseConstructor::FIELD_OPTIONS]);
        unset($data[SherlockResponseConstructor::FIELD_LOGIC_NAME]);
        $options = array_merge($options, $data);

        $response = new DefaultSherlockResponse($sherlockService, $logicName, $sherlockRequest);
        $response->setOptions($options);

        if (!empty($formatterObject)) {
            $response->setFormatter($formatterObject);
        }

        return $response;
    }

}
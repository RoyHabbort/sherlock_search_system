<?php

namespace App\V3\Services\Sherlock\SherlockResponse\ResponseFormatter;

class DefaultFormatter implements ResponseFormatter
{

    const DEFAULT_ENTITY_NAME = 'garbage';

    protected $entityName = self::DEFAULT_ENTITY_NAME;

    public function __construct(string $entityName = '')
    {
        if (!empty($entityName)) {
            $this->entityName = $entityName;
        }
    }

    /**
     * да нихера не делать. как есть, так и сохранять
     * @inheritDoc
     */
    public function convert($inputData)
    {
        return $inputData;
    }

    /**
     * @return string
     */
    public function getEntityName(): string {
        return $this->entityName;
    }
}
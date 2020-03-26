<?php

namespace App\V3\Services\Sherlock\SherlockLogic;

use App\V3\Services\Sherlock\SherlockRequest;
use App\V3\Services\Sherlock\SherlockService;

abstract class AbstractSherlockLogic implements SherlockLogic
{
    /** @var SherlockService */
    protected $service;

    abstract public function seek(SherlockRequest $request, array $options = []);

    /**
     * @return SherlockService
     */
    public function getService(): SherlockService
    {
        return $this->service;
    }

    /**
     * @param SherlockService $service
     * @return AbstractSherlockLogic
     */
    public function setService(SherlockService $service): AbstractSherlockLogic
    {
        $this->service = $service;
        return $this;
    }

}
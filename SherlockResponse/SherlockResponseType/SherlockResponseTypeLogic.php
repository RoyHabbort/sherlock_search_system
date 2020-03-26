<?php

namespace App\V3\Services\Sherlock\SherlockResponse\SherlockResponseType;

use App\V3\Services\Sherlock\Exception\SherlockResponseException;
use App\V3\Services\Sherlock\SherlockResponse\AbstractSherlockResponse;

class SherlockResponseTypeLogic implements SherlockResponseType
{

    /** @var AbstractSherlockResponse */
    protected $owner;
    /** @var string */
    protected $logicName;
    /** @var mixed */
    protected $result;
    /** @var bool */
    protected $hasResult = false;

    public function __construct(string $logicName)
    {
        $this->logicName = $logicName;
    }

    public function setOwner(AbstractSherlockResponse $owner): SherlockResponseType {
        $this->owner = $owner;
        return $this;
    }

    public function getOwner(): AbstractSherlockResponse
    {
        return $this->owner;
    }

    public function hasResult(): bool
    {
        return $this->hasResult;
    }

    public function getResult()
    {
        if (!$this->hasResult()) {
            $this->seek();
        }

        return $this->result;
    }

    /**
     * @throws SherlockResponseException
     * @throws \App\V3\Services\Sherlock\Exception\SherlockLogicNotRegisteredException
     */
    public function seek()
    {
        if ($this->hasResult()) {
            throw new SherlockResponseException('Cannot search again. The result is already there');
        }

        $rawResult = $this->getOwner()->getService()->seek(
            $this->logicName,
            $this->getOwner()->getRequest(),
            $this->getOwner()->getOptions()
        );

        $result = $this->getOwner()->getFormatter()->convert($rawResult);

        $this->result = $result;
        $this->hasResult = true;
    }

}
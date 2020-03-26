<?php

namespace App\V3\Services\Sherlock\SherlockResponse\SherlockResponseType;

use App\V3\Services\Sherlock\Exception\SherlockResponseException;
use App\V3\Services\Sherlock\SherlockResponse\AbstractSherlockResponse;

class CompositeSherlockResponseType implements SherlockResponseType
{

    /** @var AbstractSherlockResponse */
    protected $owner;
    /** @var bool */
    protected $hasResult = false;
    /** @var array  */
    protected $children = [];

    /**
     * @param AbstractSherlockResponse $owner
     * @return SherlockResponseType
     */
    public function setOwner(AbstractSherlockResponse $owner): SherlockResponseType {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @param string $childName
     * @param AbstractSherlockResponse $sherlockResponse
     * @return CompositeSherlockResponseType
     */
    public function addChildren(string $childName, AbstractSherlockResponse $sherlockResponse): CompositeSherlockResponseType
    {
        $sherlockResponse->setParent($this->getOwner());
        $this->children[$childName] = $sherlockResponse;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasResult(): bool
    {
        return $this->hasResult;
    }

    /**
     * @return AbstractSherlockResponse
     */
    public function getOwner(): AbstractSherlockResponse
    {
        return $this->owner;
    }

    /**
     * @return mixed
     * @throws SherlockResponseException
     */
    public function getResult()
    {
        if (!$this->hasResult()) {
            $this->seek();
        }

        $rawResult = [];
        foreach($this->children as $childName => $child) {
            $rawResult[$childName] = $child->getResult();
        }

        return $this->getOwner()->getFormatter()->convert($rawResult);
    }

    /**
     * @throws SherlockResponseException
     */
    public function seek()
    {
        if ($this->hasResult()) {
            throw new SherlockResponseException('Cannot search again. The result is already there');
        }

        /**
         * @var string $childName
         * @var AbstractSherlockResponse $child
         */
        foreach($this->children as $childName => $child) {
            $child->seek();
            //если после поиска, ответа так и нет, значит вся логика пошла похую. чините
            if(!$child->hasResult()) {
                throw new SherlockResponseException('Child seek has errors. No response has received');
            }
        }
        $this->hasResult = true;
    }

}
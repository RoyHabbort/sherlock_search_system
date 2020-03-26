<?php

namespace App\V3\Services\Sherlock\SherlockResponse;

class DefaultSherlockResponse extends AbstractSherlockResponse
{

    public function seek(): void
    {
        $this->getType()->seek();
    }

    public function hasResult(): bool
    {
        return $this->getType()->hasResult();
    }

    public function getResult()
    {
        return $this->getType()->getResult();
    }

}
<?php

namespace App\V3\Services\Sherlock\SherlockResponse\SherlockResponseType;

use App\V3\Services\Sherlock\SherlockResponse\AbstractSherlockResponse;

interface SherlockResponseType
{

    public function setOwner(AbstractSherlockResponse $owner): SherlockResponseType;

    public function getOwner(): AbstractSherlockResponse;

    public function getResult();

    public function hasResult(): bool;

    public function seek();
}
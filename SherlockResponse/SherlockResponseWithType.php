<?php

namespace App\V3\Services\Sherlock\SherlockResponse;

use App\V3\Services\Sherlock\SherlockResponse\SherlockResponseType\SherlockResponseType;

interface SherlockResponseWithType
{

    public function getType(): SherlockResponseType;

    public function setType(SherlockResponseType $sherlockResponseType): SherlockResponseWithType;
}
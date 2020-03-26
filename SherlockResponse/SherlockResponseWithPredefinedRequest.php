<?php

namespace App\V3\Services\Sherlock\SherlockResponse;

use App\V3\Services\Sherlock\SherlockRequest;

interface SherlockResponseWithPredefinedRequest
{

    public function getRequest(): SherlockRequest;

}
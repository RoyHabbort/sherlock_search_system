<?php

namespace App\V3\Services\Sherlock\SherlockResponse;

use App\V3\Services\Sherlock\SherlockResponse\ResponseFormatter\ResponseFormatter;

interface SherlockResponseWithFormatter
{

    public function getFormatter(): ResponseFormatter;

    public function setFormatter(ResponseFormatter $responseFormatter): SherlockResponseWithFormatter;

}
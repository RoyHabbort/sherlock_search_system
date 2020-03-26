<?php

namespace App\V3\Services\Sherlock\Helpers;

use App\V3\Services\Sherlock\SherlockResponse\DefaultSherlockResponse;
use App\V3\Services\Sherlock\SherlockResponse\ResponseFormatter\DefaultFormatter;
use App\V3\Services\Sherlock\SherlockResponse\ResponseFormatter\ResponseFormatter;

class SherlockResponseHelper
{

    /**
     * @return DefaultSherlockResponse
     */
    public static function getDefaultResponse() : DefaultSherlockResponse {
        $response = new DefaultSherlockResponse();
        $response->setFormatter(new DefaultFormatter());
        return $response;
    }

    /**
     * @return ResponseFormatter
     */
    public static function getDefaultFormatter(): ResponseFormatter {
        return new DefaultFormatter();
    }

}
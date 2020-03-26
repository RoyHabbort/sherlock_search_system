<?php

namespace App\V3\Services\Sherlock\SherlockResponse\ResponseFormatter;

interface ResponseFormatter
{

    /**
     * @param mixed $inputData
     * @return mixed
     */
    public function convert($inputData);

}
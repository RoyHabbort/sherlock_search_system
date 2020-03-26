<?php

namespace App\V3\Services\Sherlock\SherlockLogic;

use App\V3\Services\Sherlock\SherlockRequest;
use App\V3\Services\Sherlock\SherlockService;

interface SherlockLogic
{

    public function setService(SherlockService $sherlockService);

    public function getService(): SherlockService;

    public function seek(SherlockRequest $request, array $options = []);

}
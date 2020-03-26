<?php

namespace App\V3\Services\Sherlock\Constructors;

use App\V3\Services\Sherlock\SherlockRequest;
use App\V3\Services\Sherlock\SherlockResponse\AbstractSherlockResponse;
use App\V3\Services\Sherlock\SherlockService;

interface SherlockResponseConstructor
{

    const FIELD_FORMATTER = 'formatter';
    const FIELD_LOGIC_NAME = 'logic_name';
    const FIELD_OPTIONS = 'options';

    public function create(SherlockService $sherlockService, array $data, SherlockRequest $sherlockRequest): AbstractSherlockResponse;

}
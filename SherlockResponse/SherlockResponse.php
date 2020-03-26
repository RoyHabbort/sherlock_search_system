<?php

namespace App\V3\Services\Sherlock\SherlockResponse;

use App\V3\Services\Sherlock\SherlockRequest;

interface SherlockResponse
{
    public function seek(): void;

    public function hasResult(): bool;

    public function getResult();
}
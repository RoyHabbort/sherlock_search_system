<?php

namespace App\V3\Services\Sherlock;

interface SherlockRequest
{
    public function getHash(): string;

    public function getBody(): array;

    public function getParam(string $paramName, $default = null);
}
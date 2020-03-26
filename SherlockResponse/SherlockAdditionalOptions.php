<?php

namespace App\V3\Services\Sherlock\SherlockResponse;

trait SherlockAdditionalOptions
{

    /** @var array */
    protected $options = [];

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return mixed
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }
}
<?php

namespace App\V3\Services\Sherlock;


use App\V3\Services\Sherlock\Exception\SherlockRequestException;

class SherlockSimpleRequest implements SherlockRequest
{

    /** @var array  */
    protected $body = [];

    public function __construct(array $body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        $body = $this->getBody();
        $string = json_encode($body);
        return md5($string);
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @param string $paramName
     * @param null $default
     * @return mixed|null
     * @throws SherlockRequestException
     */
    public function getParam(string $paramName, $default = null)
    {
        if (!isset($this->body[$paramName])) {
            if (!is_null($default)) {
               return $default;
            }
            else {
                throw new SherlockRequestException("The request does not contain the requested parameter {$paramName}");
            }
        }
        return $this->body[$paramName];
    }
}
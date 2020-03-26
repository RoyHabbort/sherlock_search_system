<?php

namespace App\V3\Services\Sherlock\SherlockResponse;

use App\V3\Services\Sherlock\Exception\SherlockResponseException;
use App\V3\Services\Sherlock\Helpers\SherlockResponseHelper;
use App\V3\Services\Sherlock\SherlockRequest;
use App\V3\Services\Sherlock\SherlockResponse\ResponseFormatter\ResponseFormatter;
use App\V3\Services\Sherlock\SherlockResponse\SherlockResponseType\SherlockResponseType;
use App\V3\Services\Sherlock\SherlockResponse\SherlockResponseType\SherlockResponseTypeFactory;
use App\V3\Services\Sherlock\SherlockService;

abstract class AbstractSherlockResponse implements
    SherlockResponse,
    SherlockResponseWithFormatter,
    SherlockResponseWithType,
    SherlockResponseWithPredefinedRequest
{

    use SherlockAdditionalOptions;

    /** @var SherlockResponseType */
    protected $responseType;
    /** @var SherlockService */
    protected $sherlockService;
    /** @var ResponseFormatter */
    protected $formatter;
    /** @var SherlockRequest */
    protected $request;
    /** @var AbstractSherlockResponse */
    protected $parent;

    public function __construct(
        SherlockService $sherlockService,
        string $sherlockResponseTypeName,
        SherlockRequest $sherlockRequest
    ) {
        $sherlockResponseType = SherlockResponseTypeFactory::factory($sherlockResponseTypeName);

        $this
            ->setRequest($sherlockRequest)
            ->setService($sherlockService)
            ->setType($sherlockResponseType);
    }

    abstract public function seek(): void;

    abstract public function hasResult(): bool;

    abstract public function getResult();

    /**
     * @return AbstractSherlockResponse
     */
    public function getParent(): AbstractSherlockResponse
    {
        return $this->parent;
    }

    /**
     * @param AbstractSherlockResponse $parent
     * @return AbstractSherlockResponse
     */
    public function setParent(AbstractSherlockResponse $parent): AbstractSherlockResponse
    {
        $this->parent = $parent;
        return $this;
    }

    protected function setRequest(SherlockRequest $sherlockRequest): AbstractSherlockResponse
    {
        $this->request = $sherlockRequest;
        return $this;
    }

    public function getRequest(): SherlockRequest
    {
        return $this->request;
    }

    protected function setService(SherlockService $sherlockService): AbstractSherlockResponse
    {
        $this->sherlockService = $sherlockService;
        return $this;
    }

    public function getService(): SherlockService
    {
        return $this->sherlockService;
    }

    /**
     * @param SherlockResponseType $sherlockResponseType
     * @return SherlockResponseWithType
     * @throws SherlockResponseException
     */
    public function setType(SherlockResponseType $sherlockResponseType): SherlockResponseWithType
    {
        if (!empty($this->responseType)) {
            throw new SherlockResponseException('Response already have type. Type cannot be changed');
        }

        //Замыкаем
        $sherlockResponseType->setOwner($this);

        $this->responseType = $sherlockResponseType;
        return $this;
    }

    public function getType(): SherlockResponseType
    {
        return $this->responseType;
    }

    public function setFormatter(ResponseFormatter $formatter): SherlockResponseWithFormatter
    {
        $this->formatter = $formatter;
        return $this;
    }

    public function getFormatter(): ResponseFormatter
    {
        //с одной стороны не очевидно. С другой, он нихера не делает. Просто отдаёт теже данные.
        //да в будущем переделать. ну вот вам даже @todo.
        if (empty($this->formatter)) {
            $this->setFormatter(SherlockResponseHelper::getDefaultFormatter());
        }

        return $this->formatter;
    }
}
<?php

namespace App\V3\Services\Sherlock;

use App\V3\Services\Sherlock\Exception\SherlockLogicException;
use App\V3\Services\Sherlock\Exception\SherlockLogicNotRegisteredException;
use App\V3\Services\Sherlock\SherlockLogic\SherlockLogic;

class SherlockService
{

    /** @var array  */
    protected $logicMap = [];
    /** @var bool  */
    protected $useStrict = false;

    /**
     * @param string $templateName
     * @param SherlockRequest $request
     * @param array $options
     * @return array
     * @throws SherlockLogicException
     * @throws SherlockLogicNotRegisteredException
     */
    public function seek(string $templateName, SherlockRequest $request, array $options = [])
    {
        //@todo: сдесь сделать кеширвание. логирование. и систему событий
        $logic = $this->getLogic($templateName);

        try {
            $result = $logic->seek($request, $options);
        }
        catch (SherlockLogicException $sherlockLogicException) {
            //если указан строгий режим, то пропускаем ошибку дальше. Чтобы её видел и "клиент"
            if ($this->useStrict) {
                throw $sherlockLogicException;
            }
            else {
                //иначе, просто считаем что ничего не нашлось
                $result = [];
            }
        }

        return $result;
    }

    public function useStrict(bool $bool = true): SherlockService
    {
        $this->useStrict = $bool;
        return $this;
    }

    /**
     * @param string $templateName
     * @return bool
     */
    public function hasLogic(string $templateName): bool
    {
        return isset($this->logicMap[$templateName]);
    }

    /**
     * @param string $templateName
     * @param SherlockLogic $logic
     * @return $this
     */
    public function setLogic(string $templateName, SherlockLogic $logic)
    {
        $logic->setService($this);
        $this->logicMap[$templateName] = $logic;
        return $this;
    }

    /**
     * @param string $templateName
     * @return SherlockLogic
     * @throws SherlockLogicNotRegisteredException
     */
    public function getLogic(string $templateName): SherlockLogic
    {
        if (!isset($this->logicMap[$templateName])) {
            throw new SherlockLogicNotRegisteredException("Logic for {$templateName} not registered in service");
        }
        return $this->logicMap[$templateName];
    }
}
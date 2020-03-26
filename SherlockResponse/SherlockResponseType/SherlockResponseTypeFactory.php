<?php

namespace App\V3\Services\Sherlock\SherlockResponse\SherlockResponseType;

class SherlockResponseTypeFactory
{

    /**
     * @param string $typeString
     * @return SherlockResponseType
     */
    public static function factory(string $typeString): SherlockResponseType {

        if ($typeString === CompositeSherlockResponseType::class) {
            return new CompositeSherlockResponseType;
        }
        else {
            return new SherlockResponseTypeLogic($typeString);
        }
    }

}
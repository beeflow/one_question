<?php

namespace App\TransportObject;

class InvalidParamFactory
{
    /**
     * @return InvalidParam
     */
    public function getInvalidParam(): InvalidParam
    {
        return new InvalidParam($this);
    }
}
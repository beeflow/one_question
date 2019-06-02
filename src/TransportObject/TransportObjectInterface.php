<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\TransportObject;

use InvalidArgumentException;

interface TransportObjectInterface
{
    /**
     * @param array $params
     *
     * @return TransportObjectInterface
     * @throws InvalidArgumentException
     */
    public function prepareFromArray(array $params): TransportObjectInterface;

    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @return string
     */
    public function getLastError(): string;

    /**
     * @return array
     */
    public function getErrors(): array;

    /**
     * @return InvalidParam[]
     */
    public function getInvalidParams(): array;
}
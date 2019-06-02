<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\TransportObject\TransportObjects;

use App\FieldNames\CurrencyFields;
use App\TransportObject\AbstractTransportObject;
use App\TransportObject\TransportObjectInterface;
use InvalidArgumentException;

class Currency extends AbstractTransportObject implements TransportObjectInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $symbol;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    /**
     * @param array $params
     *
     * @return TransportObjectInterface
     * @throws InvalidArgumentException
     */
    public function prepareFromArray(array $params): TransportObjectInterface
    {
        $this->setName($params[ CurrencyFields::NAME ] ?? '');
        $this->setCode($params[ CurrencyFields::CODE ] ?? '');
        $this->setSymbol($params[ CurrencyFields::SYMBOL ] ?? '');

        if (!$this->isValid()) {
            throw new InvalidArgumentException(
                $this->translator->trans('transport_object.errors.invalid_params')
            );
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->getInvalidParams());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            CurrencyFields::NAME   => $this->name,
            CurrencyFields::CODE   => $this->code,
            CurrencyFields::SYMBOL => $this->symbol
        ];
    }

    /**
     * @param string $name
     */
    private function setName(string $name): void
    {
        if (!$this->isStringElementValid(CurrencyFields::NAME, $name, 150)) {
            return;
        }

        $this->name = $name;
    }

    /**
     * @param string $symbol
     */
    private function setSymbol(string $symbol): void
    {
        if (empty($symbol)) {
            return;
        }

        if (mb_strlen($symbol) <= 10) {
            $this->symbol = $symbol;

            return;
        }

        $this->addInvalidParam(
            $this->invalidParamFactory->getInvalidParam()
                ->setName(CurrencyFields::SYMBOL)
                ->setReason(
                    sprintf(
                        $this->translator->trans('transport_object.errors.string_element_is_too_long'),
                        CurrencyFields::SYMBOL,
                        10
                    )
                )
        );
    }

    /**
     * @param string $code
     */
    private function setCode(string $code): void
    {
        if (!$this->isStringElementValid(CurrencyFields::CODE, $code, 3)) {
            return;
        }

        $this->code = $code;
    }
}
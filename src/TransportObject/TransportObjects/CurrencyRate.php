<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\TransportObject\TransportObjects;

use App\FieldNames\CurrencyRateFields;
use App\TransportObject\AbstractTransportObject;
use App\TransportObject\TransportObjectInterface;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;

class CurrencyRate extends AbstractTransportObject implements TransportObjectInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var float
     */
    private $rate;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @param array $params
     *
     * @return TransportObjectInterface
     * @throws InvalidArgumentException
     */
    public function prepareFromArray(array $params): TransportObjectInterface
    {
        $this->code = $params[ CurrencyRateFields::CODE ] ?? null;
        $this->rate = (float) $params[ CurrencyRateFields::MID ] ?? null;

        try {
            $this->date = new \DateTimeImmutable($params[ CurrencyRateFields::RATING_DATE ] ?? null);
        } catch (Exception $exception) {
            /** do nothing */
        }

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
        $isValid = true;

        if (is_null($this->rate)) {
            $this->addInvalidParam(
                $this->invalidParamFactory->getInvalidParam()
                    ->setName(CurrencyRateFields::MID)
                    ->setReason(
                        sprintf(
                            $this->translator->trans('transport_object.errors.element_cannot_be_empty'),
                            CurrencyRateFields::MID
                        )
                    )
            );

            $isValid = false;
        }

        if (is_null($this->code)) {
            $this->addInvalidParam(
                $this->invalidParamFactory->getInvalidParam()
                    ->setName(CurrencyRateFields::CODE)
                    ->setReason(
                        sprintf(
                            $this->translator->trans('transport_object.errors.element_cannot_be_empty'),
                            CurrencyRateFields::CODE
                        )
                    )
            );

            $isValid = false;
        }

        if (is_null($this->date)) {
            $this->addInvalidParam(
                $this->invalidParamFactory->getInvalidParam()
                    ->setName(CurrencyRateFields::RATING_DATE)
                    ->setReason(
                        sprintf(
                            $this->translator->trans('transport_object.errors.element_cannot_be_empty'),
                            CurrencyRateFields::RATING_DATE
                        )
                    )
            );

            $isValid = false;
        }

        return $isValid;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            CurrencyRateFields::CODE        => $this->code,
            CurrencyRateFields::RATE        => $this->rate,
            CurrencyRateFields::RATING_DATE => $this->date
        ];
    }
}
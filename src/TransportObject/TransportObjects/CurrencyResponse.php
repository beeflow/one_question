<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\TransportObject\TransportObjects;

use App\FieldNames\CurrencyFields;
use App\FieldNames\CurrencyRateFields;
use App\TransportObject\AbstractTransportObject;
use App\TransportObject\TransportObjectInterface;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;

class CurrencyResponse extends AbstractTransportObject
{
    /**
     * @var string
     */
    private $currencyName;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var string|null
     */
    private $currencySymbol;

    /**
     * @var DateTimeImmutable
     */
    private $rateDate;

    /**
     * @var float|null
     */
    private $rate;

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @param array $params
     *
     * @return TransportObjectInterface
     * @throws InvalidArgumentException
     */
    public function prepareFromArray(array $params): TransportObjectInterface
    {
        $this->currencyName = $params['currency_name'] ?? null;
        $this->currencyCode = $params['currency_code'] ?? null;
        $this->currencySymbol = $params['currency_symbol'] ?? null;
        $this->rate = (float) $params['rate'] ?? null;

        try {
            $this->rateDate = new DateTimeImmutable($params['rating_date'] ?? null);
        } catch (Exception $exception) {
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

        if (is_null($this->currencyName)) {
            $isValid = false;
        }

        if (is_null($this->currencyCode)) {
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
            CurrencyFields::NAME            => $this->currencyName,
            CurrencyFields::CODE            => $this->currencyCode,
            CurrencyFields::SYMBOL          => $this->currencySymbol,
            CurrencyRateFields::RATING_DATE => is_null($this->rateDate) ? '' : $this->rateDate->format('Y-m-d'),
            CurrencyRateFields::RATE        => $this->rate
        ];
    }
}
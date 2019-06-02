<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\TransportObject\TransportObjects;

use App\FieldNames\CountryFields;
use App\TransportObject\AbstractTransportObject;
use App\TransportObject\TransportObjectInterface;
use InvalidArgumentException;

class Country extends AbstractTransportObject implements TransportObjectInterface
{
    /**
     * @var string
     */
    private $countryName;

    /**
     * @var string
     */
    private $countryNativeName;

    /**
     * @var Currency[]
     */
    private $currencies;

    /**
     * @return string
     */
    public function getCountryName(): string
    {
        return $this->countryName;
    }

    /**
     * @return string
     */
    public function getCountryNativeName(): string
    {
        return $this->countryNativeName;
    }

    /**
     * @return Currency[]
     */
    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    /**
     * @param array $params
     *
     * @return TransportObjectInterface
     * @throws InvalidArgumentException
     */
    public function prepareFromArray(array $params): TransportObjectInterface
    {
        $this->setName($params[ CountryFields::NAME ] ?? '');
        $this->setNativeName($params[ CountryFields::NATIVE_NAME ] ?? '');
        $this->prepareCurrencies($params[ CountryFields::CURRENCIES ] ?? []);

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
            CountryFields::NAME        => $this->countryName,
            CountryFields::NATIVE_NAME => $this->countryNativeName,
            CountryFields::CURRENCIES  => $this->getCurrenciesArray()
        ];
    }

    /**
     * @param Currency ...$currencies
     *
     * @return Country
     */
    public function setCurrencies(Currency ...$currencies): Country
    {
        $this->currencies = $currencies;

        return $this;
    }

    /**
     * @param array $currencies
     */
    private function prepareCurrencies(array $currencies): void
    {
        foreach ($currencies as $currencyData) {
            $currency = new Currency($this->invalidParamFactory, $this->translator);

            try {
                $currency->prepareFromArray($currencyData);
                $this->currencies[] = $currency;
            } catch (InvalidArgumentException $exception) {
                continue;
            }
        }
    }

    /**
     * @param string $name
     */
    private function setName(string $name): void
    {
        if (!$this->isStringElementValid(CountryFields::NAME, $name, 255)) {
            return;
        }

        $this->countryName = $name;
    }

    /**
     * @param string $name
     */
    private function setNativeName(string $name): void
    {
        if (!$this->isStringElementValid(CountryFields::NATIVE_NAME, $name, 255)) {
            return;
        }

        $this->countryNativeName = $name;
    }

    /**
     * @return array
     */
    private function getCurrenciesArray(): array
    {
        if (empty($this->currencies)) {
            return [];
        }

        $result = [];

        foreach ($this->currencies as $currency) {
            $result[] = $currency->toArray();
        }

        return $result;
    }
}
<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\Factories;

use App\Entity\Country;
use App\Entity\Currency;
use App\Entity\CurrencyRating;
use App\TransportObject\TransportObjects\Currency as CurrencyTransportObject;
use App\TransportObject\TransportObjects\Country as CountryTransportObject;
use App\TransportObject\TransportObjects\CurrencyRate as CurrencyRateTransportObject;

class EntityFactory
{
    /**
     * @param CountryTransportObject|null $country
     *
     * @return Country
     */
    public function createCountry(?CountryTransportObject $country): Country
    {
        if (is_null($country)) {
            return new Country();
        }

        return Country::createFrom($country);
    }

    /**
     * @param CurrencyTransportObject|null $currency
     *
     * @return Currency
     */
    public function createCurrency(?CurrencyTransportObject $currency): Currency
    {
        if (is_null($currency)) {
            return new Currency();
        }

        return Currency::createFrom($currency);
    }

    /**
     * @param CurrencyRateTransportObject|null $currencyRate
     *
     * @return CurrencyRating
     */
    public function createCurrencyRating(?CurrencyRateTransportObject $currencyRate): CurrencyRating
    {
        if (is_null($currencyRate)) {
            return new CurrencyRating();
        }

        return CurrencyRating::createFrom($currencyRate);
    }
}
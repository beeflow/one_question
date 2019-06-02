<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\Factories;

use App\TransportObject\InvalidParamFactory;
use App\TransportObject\TransportObjects\Country;
use App\TransportObject\TransportObjects\Currency;
use App\TransportObject\TransportObjects\CurrencyRate;
use App\TransportObject\TransportObjects\CurrencyResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class TransportObjectFactory
{
    /**
     * @var InvalidParamFactory
     */
    private $invalidParamFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param InvalidParamFactory $invalidParamFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(InvalidParamFactory $invalidParamFactory, TranslatorInterface $translator)
    {
        $this->invalidParamFactory = $invalidParamFactory;
        $this->translator = $translator;
    }

    /**
     * @return Country
     */
    public function createCountry(): Country
    {
        return new Country($this->invalidParamFactory, $this->translator);
    }

    /**
     * @return Currency
     */
    public function createCurrency(): Currency
    {
        return new Currency($this->invalidParamFactory, $this->translator);
    }

    /**
     * @return CurrencyRate
     */
    public function createCurrencyRate(): CurrencyRate
    {
        return new CurrencyRate($this->invalidParamFactory, $this->translator);
    }

    /**
     * @return CurrencyResponse
     */
    public function createCurrencyResponse(): CurrencyResponse
    {
        return new CurrencyResponse($this->invalidParamFactory, $this->translator);
    }
}
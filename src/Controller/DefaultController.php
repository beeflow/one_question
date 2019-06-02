<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Currency;
use App\Entity\CurrencyRating;
use App\Factories\TransportObjectFactory;
use App\FieldNames\CountryFields;
use App\Libs\ApiResponse\ApiResponse;
use App\TransportObject\TransportObjects\CurrencyResponse;
use Doctrine\DBAL\DBALException;
use RuntimeException;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var TransportObjectFactory
     */
    private $transportObjectFactory;

    /**
     * @var CurrencyRating[]
     */
    private $ratings = [];

    /**
     * @param ManagerRegistry        $managerRegistry
     * @param TransportObjectFactory $transportObjectFactory
     */
    public function __construct(ManagerRegistry $managerRegistry, TransportObjectFactory $transportObjectFactory)
    {
        $this->managerRegistry = $managerRegistry;
        $this->transportObjectFactory = $transportObjectFactory;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $countries = $this->managerRegistry->getRepository(Country::class)
            ->findAll();

        $result = [];

        foreach ($countries as $country) {
            $result[] = [
                CountryFields::NAME        => $country->getCountryName(),
                CountryFields::NATIVE_NAME => $country->getCountryNativeName(),
                CountryFields::CURRENCIES  => $this->getCountryCurrencies($country)
            ];
        }

        return (new ApiResponse())
            ->setData($result)
            ->getResponse();
    }

    /**
     * @param string $countryName
     *
     * @return Response
     */
    public function find(string $countryName): Response
    {
        $countries = $this->managerRegistry->getRepository(Country::class)
            ->findBy(['countryName' => mb_convert_case($countryName, MB_CASE_TITLE)]);

        $result = [];

        foreach ($countries as $country) {
            $result = [
                CountryFields::NAME        => $country->getCountryName(),
                CountryFields::NATIVE_NAME => $country->getCountryNativeName(),
                CountryFields::CURRENCIES  => $this->getCountryCurrencies($country)
            ];
        }

        return (new ApiResponse())
            ->setData($result)
            ->getResponse();
    }

    /**
     * @param Country $country
     *
     * @return Currency[]
     */
    private function getCountryCurrencies(Country $country): array
    {
        $currencies = $country->getCurrency();
        $result = [];

        foreach ($currencies as $currency) {
            try {
                $result[] = $this->getRateByCurrency($currency)->toArray();
            } catch (RuntimeException|DBALException $exception) {
            }
        }

        return $result;
    }

    /**
     * @param Currency $currency
     *
     * @return CurrencyResponse
     * @throws RuntimeException
     * @throws DBALException
     */
    private function getRateByCurrency(Currency $currency): CurrencyResponse
    {
        if (empty($this->ratings)) {
            $ratings = $this->managerRegistry->getRepository(CurrencyRating::class)
                ->findByMaxDate($this->transportObjectFactory);

            foreach ($ratings as $rating) {
                $this->ratings[ $rating->getCurrencyCode() ] = $rating;
            }
        }

        if (in_array($currency->getCurrencyCode(), array_keys($this->ratings))) {
            return $this->ratings[ $currency->getCurrencyCode() ];
        }

        throw new RuntimeException(
            sprintf('Currency for code %s not found,', $currency->getCurrencyCode())
        );
    }
}
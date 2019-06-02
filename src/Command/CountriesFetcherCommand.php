<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\Command;

use App\Entity\Country as CountryEntity;
use App\Entity\Currency as CurrencyEntity;
use App\Factories\EntityFactory;
use App\Factories\TransportObjectFactory;
use App\TransportObject\TransportObjects\Country;
use App\TransportObject\TransportObjects\Currency;
use Doctrine\Migrations\Configuration\Exception\JsonNotValid;
use Exception;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CountriesFetcherCommand extends Command
{
    private const COMMAND_NAME        = 'countries:fetch';
    private const COMMAND_DESCRIPTION = 'Transfer list of countries';

    /**
     * @var string
     */
    private $countriesApiUrl;

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var TransportObjectFactory
     */
    private $transportObjectFactory;

    /**
     * @param string                 $currenciesApiUrl
     * @param ManagerRegistry        $objectManager
     * @param EntityFactory          $entityFactory
     * @param TransportObjectFactory $transportObjectFactory
     */
    public function __construct(
        string $currenciesApiUrl,
        ManagerRegistry $objectManager,
        EntityFactory $entityFactory,
        TransportObjectFactory $transportObjectFactory
    )
    {
        parent::__construct(self::COMMAND_NAME);

        $this->countriesApiUrl = $currenciesApiUrl;
        $this->managerRegistry = $objectManager;
        $this->entityFactory = $entityFactory;
        $this->transportObjectFactory = $transportObjectFactory;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription(self::COMMAND_DESCRIPTION);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws JsonNotValid|InvalidArgumentException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $fileContent = file_get_contents($this->countriesApiUrl);
        } catch (Exception $exception) {
            throw new InvalidArgumentException("Incorrect URL!");
        }

        $countries = json_decode($fileContent, true);

        if (is_null($countries)) {
            throw new JsonNotValid("Received incorrect JSON data!");
        }

        $progress = new ProgressBar($output, count($countries));
        $progress->setFormat('verbose');

        foreach ($countries as $countryData) {
            $country = $this->transportObjectFactory->createCountry();
            $country->prepareFromArray($countryData);

            $this->createCountry($country);
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');
    }

    /**
     * @param Country $country
     */
    private function createCountry(Country $country): void
    {
        $countryEntity = $this->managerRegistry
            ->getRepository(CountryEntity::class)
            ->findOneBy(['countryName' => $country->getCountryName()]);

        if (!$countryEntity instanceof CountryEntity) {
            $countryEntity = $this->entityFactory->createCountry($country);
        }

        $currencies = [];

        foreach ($country->getCurrencies() as $currency) {
            $currencies[ $currency->getCode() ] = $currency;
        }

        foreach ($currencies as $currency) {
            $currencyEntity = $this->createCurrency($currency);
            $countryEntity->addCurrency($currencyEntity);
        }

        $this->managerRegistry->getManager()->persist($countryEntity);
        $this->managerRegistry->getManager()->flush();
    }

    /**
     * @param Currency $currency
     *
     * @return CurrencyEntity
     */
    private function createCurrency(Currency $currency): CurrencyEntity
    {
        $currencyEntity = $this->managerRegistry
            ->getRepository(CurrencyEntity::class)
            ->findOneBy(['currencyCode' => $currency->getCode()]);

        if ($currencyEntity instanceof CurrencyEntity) {
            return $currencyEntity;
        }

        $currencyEntity = $this->entityFactory->createCurrency($currency);
        $this->managerRegistry->getManager()->persist($currencyEntity);

        return $currencyEntity;
    }
}
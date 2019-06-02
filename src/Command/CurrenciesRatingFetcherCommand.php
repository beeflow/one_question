<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\Command;

use App\Entity\Currency;
use App\Entity\CurrencyRating;
use App\Factories\EntityFactory;
use App\Factories\TransportObjectFactory;
use App\FieldNames\CurrencyRateFields;
use App\TransportObject\TransportObjects\CurrencyRate;
use DateTimeImmutable;
use Doctrine\Migrations\Configuration\Exception\JsonNotValid;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerTrait;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurrenciesRatingFetcherCommand extends Command
{
    private const COMMAND_NAME        = 'currencies:fetch';
    private const COMMAND_DESCRIPTION = 'Transfer rates of currencies';

    /**
     * @var string
     */
    private $currenciesApiUrl;

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

        $this->currenciesApiUrl = $currenciesApiUrl;
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
            $fileContent = file_get_contents($this->currenciesApiUrl);
        } catch (Exception $exception) {
            throw new InvalidArgumentException("Incorrect URL!");
        }

        $currencies = json_decode($fileContent, true);

        if (is_null($currencies)) {
            throw new JsonNotValid("Received incorrect JSON data!");
        }

        $effectiveDate = $currencies[0]['effectiveDate'];
        $rates = $currencies[0]['rates'];

        $progress = new ProgressBar($output, count($rates));
        $progress->setFormat('verbose');

        foreach ($rates as $rate) {
            $rate[ CurrencyRateFields::RATING_DATE ] = $effectiveDate;
            $currencyRate = $this->transportObjectFactory->createCurrencyRate();
            $currencyRate->prepareFromArray($rate);

            $currency = $this->managerRegistry->getRepository(Currency::class)
                ->findOneBy(['currencyCode' => $rate[ CurrencyRateFields::CODE ]]);

            if (!$currency instanceof Currency) {
                continue;
            }

            $currencyRateEntity = $this->createCurrencyRating($currencyRate, $currency);
            $currencyRateEntity->setCurrency($currency);

            $this->managerRegistry->getManager()->persist($currencyRateEntity);
        }

        $this->managerRegistry->getManager()->flush();

        $progress->finish();
        $output->writeln('');
    }

    /**
     * @param CurrencyRate $currencyRate
     * @param Currency     $currency
     *
     * @return CurrencyRating
     */
    private function createCurrencyRating(CurrencyRate $currencyRate, Currency $currency): CurrencyRating
    {
        $currencyRating = $this->managerRegistry->getRepository(CurrencyRating::class)
            ->findOneBy([
                'ratingDate' => $currencyRate->getDate(),
                'currency'   => $currency
            ]);

        if ($currencyRating instanceof CurrencyRating) {
            return $currencyRating;
        }

        return $this->entityFactory->createCurrencyRating($currencyRate);
    }
}
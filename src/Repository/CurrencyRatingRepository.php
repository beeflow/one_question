<?php

namespace App\Repository;

use App\Entity\CurrencyRating;
use App\Factories\TransportObjectFactory;
use App\TransportObject\TransportObjects\CurrencyResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CurrencyRating|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencyRating|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencyRating[]    findAll()
 * @method CurrencyRating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRatingRepository extends ServiceEntityRepository
{
    private const GET_ALL = '
        select 
            currency.currency_name,
            currency.currency_code,
            currency.currency_symbol,
            currency_rating.rate,
            currency_rating.rating_date 
        from currency
            left join currency_rating on currency_rating.currency_id = currency.id 
            and currency_rating.rating_date = (select max(rating_date) from currency_rating)
    ';

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CurrencyRating::class);
    }

    /**
     * @param TransportObjectFactory $transportObjectFactory
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByMaxDate(TransportObjectFactory $transportObjectFactory): array
    {
        $statement = $this->getEntityManager()->getConnection()->prepare(self::GET_ALL);
        $statement->execute();

        $ratings = $statement->fetchAll();
        $ratingsResult = [];

        if (empty($ratings)) {
            return [];
        }

        foreach ($ratings as $rating) {
            $ratingsResult[] = $transportObjectFactory->createCurrencyResponse()->prepareFromArray($rating);
        }

        return $ratingsResult;
    }
}

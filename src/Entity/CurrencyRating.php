<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\TransportObject\TransportObjects\CurrencyRate as CurrencyRateTransportObject;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @Table(
 *     uniqueConstraints={
 *        @UniqueConstraint(
 *              name="unique_currency_rate",
 *              columns={"rating_date", "currency_id"}
 *      )
 *    }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRatingRepository")
 */
class CurrencyRating
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", inversedBy="currencyRatings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $currency;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $ratingDate;

    /**
     * @ORM\Column(type="float")
     */
    private $rate;

    /**
     * @param CurrencyRateTransportObject $currencyRate
     *
     * @return CurrencyRating
     */
    static public function createFrom(CurrencyRateTransportObject $currencyRate): CurrencyRating
    {
        $self = new self();
        $self->setRate($currencyRate->getRate())
            ->setRatingDate($currencyRate->getDate());

        return $self;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getRatingDate(): ?\DateTimeImmutable
    {
        return $this->ratingDate;
    }

    public function setRatingDate(\DateTimeImmutable $ratingDate): self
    {
        $this->ratingDate = $ratingDate;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }
}

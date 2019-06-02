<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\TransportObject\TransportObjects\Currency as CurrencyTransportObject;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRepository")
 */
class Currency
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
     */
    private $currencyName;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $currencyCode;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $currencySymbol;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Country", mappedBy="currency")
     */
    private $countries;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CurrencyRating", mappedBy="currency", orphanRemoval=true)
     */
    private $currencyRatings;

    /**
     * @param CurrencyTransportObject $currency
     *
     * @return Currency
     */
    static public function createFrom(CurrencyTransportObject $currency): Currency
    {
        $self = new self();
        $self->setCurrencySymbol($currency->getSymbol())
            ->setCurrencyCode($currency->getCode())
            ->setCurrencyName($currency->getName());

        return $self;
    }

    public function __construct()
    {
        $this->countries = new ArrayCollection();
        $this->currencyRatings = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getCurrencyName(): ?string
    {
        return $this->currencyName;
    }

    /**
     * @param string $currencyName
     *
     * @return Currency
     */
    public function setCurrencyName(string $currencyName): Currency
    {
        $this->currencyName = $currencyName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     *
     * @return Currency
     */
    public function setCurrencyCode(string $currencyCode): Currency
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrencySymbol(): ?string
    {
        return $this->currencySymbol;
    }

    /**
     * @param string|null $currencySymbol
     *
     * @return Currency
     */
    public function setCurrencySymbol(?string $currencySymbol): Currency
    {
        $this->currencySymbol = $currencySymbol;

        return $this;
    }

    /**
     * @return Collection|Country[]
     */
    public function getCountries(): Collection
    {
        return $this->countries;
    }

    public function addCountry(Country $country): self
    {
        if (!$this->countries->contains($country)) {
            $this->countries[] = $country;
            $country->addCurrency($this);
        }

        return $this;
    }

    public function removeCountry(Country $country): self
    {
        if ($this->countries->contains($country)) {
            $this->countries->removeElement($country);
            $country->removeCurrency($this);
        }

        return $this;
    }

    /**
     * @return Collection|CurrencyRating[]
     */
    public function getCurrencyRatings(): Collection
    {
        return $this->currencyRatings;
    }

    public function addCurrencyRating(CurrencyRating $currencyRating): self
    {
        if (!$this->currencyRatings->contains($currencyRating)) {
            $this->currencyRatings[] = $currencyRating;
            $currencyRating->setCurrency($this);
        }

        return $this;
    }

    public function removeCurrencyRating(CurrencyRating $currencyRating): self
    {
        if ($this->currencyRatings->contains($currencyRating)) {
            $this->currencyRatings->removeElement($currencyRating);
            // set the owning side to null (unless already changed)
            if ($currencyRating->getCurrency() === $this) {
                $currencyRating->setCurrency(null);
            }
        }

        return $this;
    }
}

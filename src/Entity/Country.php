<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\TransportObject\TransportObjects\Country as CountryTransportObject;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 */
class Country
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $countryName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $countryNativeName;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Currency", inversedBy="countries")
     */
    private $currency;

    /**
     * @param CountryTransportObject $country
     *
     * @return Country
     */
    static public function createFrom(CountryTransportObject $country): Country
    {
        $self = new self();
        $self->setCountryName($country->getCountryName())
            ->setCountryNativeName($country->getCountryNativeName());

        return $self;
    }
    
    public function __construct()
    {
        $this->currency = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function setCountryName(string $countryName): self
    {
        $this->countryName = $countryName;

        return $this;
    }

    public function getCountryNativeName(): ?string
    {
        return $this->countryNativeName;
    }

    public function setCountryNativeName(string $countryNativeName): self
    {
        $this->countryNativeName = $countryNativeName;

        return $this;
    }

    /**
     * @return Collection|Currency[]
     */
    public function getCurrency(): Collection
    {
        return $this->currency;
    }

    public function addCurrency(Currency $currency): self
    {
        if (!$this->currency->contains($currency)) {
            $this->currency[] = $currency;
        }

        return $this;
    }

    public function removeCurrency(Currency $currency): self
    {
        if ($this->currency->contains($currency)) {
            $this->currency->removeElement($currency);
        }

        return $this;
    }
}

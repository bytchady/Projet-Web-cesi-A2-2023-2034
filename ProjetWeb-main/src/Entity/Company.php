<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('Company')]
class Company
{
    #[Id]
    #[Column, GeneratedValue]
    private int $Id_Company;

    #[Column]
    private string $Name_Company;

    #[Column]
    private string $Business_Sector;

    #[Column (type: "string", length: 255, nullable: true)]
    private ?string $Logo_Path = null;

    #[Column(type: "boolean")]
    private bool $Del;

    #[OneToMany(targetEntity: Location::class, mappedBy: 'Company')]
    private Collection $Location;

    #[OneToMany(targetEntity: Offer::class, mappedBy: 'Company')]
    private Collection $Offer;

    #[OneToMany(mappedBy: "company", targetEntity: Evaluation::class)]
    private Collection $evaluations;

    public function __construct()
    {
        $this->evaluations = new ArrayCollection();
        $this->Offer = new ArrayCollection();
        $this->Location = new ArrayCollection();
    }
    public function getEvaluations(): Collection {
        return $this->evaluations;
    }

    public function getIdCompany(): int
    {
        return $this->Id_Company;
    }

    public function getNameCompany(): string
    {
        return $this->Name_Company;
    }

    public function setNameCompany(string $Name_Company): void
    {
        $this->Name_Company = $Name_Company;
    }

    public function getBusinessSector(): string
    {
        return $this->Business_Sector;
    }

    public function setBusinessSector(string $Business_Sector): void
    {
        $this->Business_Sector = $Business_Sector;
    }

    public function isDel(): bool
    {
        return $this->Del;
    }

    public function setDel(bool $Del): void
    {
        $this->Del = $Del;
    }

    public function getLocation(): Collection
    {
        return $this->Location;
    }

    public function getLogoPath(): ?string
    {
        return $this->Logo_Path;
    }

    public function setLogoPath(?string $LogoPath): void
    {
        $this->Logo_Path = $LogoPath;
    }

    public function setLocation(Collection $Location): void
    {
        $this->Location = $Location;
    }

    public function getOffer(): Collection
    {
        return $this->Offer;
    }

    public function setOffer(Collection $Offer): void
    {
        $this->Offer = $Offer;
    }

}
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('Location')]
class Location
{
    #[Id]
    #[Column, GeneratedValue]
    private int $Id_Location;

    #[Column]
    private int $Num_Street;

    #[Column]
    private string $Name_Street;

    #[Column]
    private string $Name_City;

    #[Column]
    private string $Add_Adr;

    #[Column]
    private string $Zipcode;

    #[Column(type: "boolean")]
    private bool $Del;

    #[ManyToOne(inversedBy: "Location")]
    #[JoinColumn(name: 'Company_Id',referencedColumnName: 'Id_Company')]
    private Company $Company;

    #[OneToMany(targetEntity: Offer::class, mappedBy: 'Location')]
    private Collection $Offer;

    public function __construct()
    {
        $this->Offer = new ArrayCollection();
    }

    public function getIdLocation(): int
    {
        return $this->Id_Location;
    }

    public function getNumStreet(): int
    {
        return $this->Num_Street;
    }

    public function setNumStreet(int $Num_Street): void
    {
        $this->Num_Street = $Num_Street;
    }

    public function getNameStreet(): string
    {
        return $this->Name_Street;
    }

    public function setNameStreet(string $Name_Street): void
    {
        $this->Name_Street = $Name_Street;
    }

    public function getNameCity(): string
    {
        return $this->Name_City;
    }

    public function setNameCity(string $Name_City): void
    {
        $this->Name_City = $Name_City;
    }

    public function getAddAdr(): string
    {
        return $this->Add_Adr;
    }

    public function setAddAdr(string $Add_Adr): void
    {
        $this->Add_Adr = $Add_Adr;
    }

    public function getZipcode(): string
    {
        return $this->Zipcode;
    }

    public function setZipcode(string $Zipcode): void
    {
        $this->Zipcode = $Zipcode;
    }

    public function isDel(): bool
    {
        return $this->Del;
    }

    public function setDel(bool $Del): void
    {
        $this->Del = $Del;
    }

    public function setCompany(Company $Company): void
    {
        $this->Company = $Company;
    }

    public function getCompany(): Company
    {
        return $this->Company;
    }

    // Dans votre classe Location
    public function getFullAddress(): string
    {
        return $this->Num_Street . ' ' . $this->Name_Street . ', ' . $this->Zipcode . ' ' . $this->Name_City . ' ' . $this->Add_Adr;
    }


}
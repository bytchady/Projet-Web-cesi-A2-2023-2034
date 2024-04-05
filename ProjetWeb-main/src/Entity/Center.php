<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('Center')]
class Center
{
    #[Id]
    #[Column, GeneratedValue]
    private int $Id_Center;

    #[Column]
    private string  $Name_Center;

    #[OneToMany(targetEntity: Promotion::class, mappedBy: 'Center')]
    private Collection $Promotion;

    public function __construct()
    {
        $this->Promotion = new ArrayCollection();
    }

    public function getIdCenter(): int
    {
        return $this->Id_Center;
    }

    public function getNameCenter(): string
    {
        return $this->Name_Center;
    }

    public function setNameCenter(string $Name_Center): void
    {
        $this->Name_Center = $Name_Center;
    }

    public function getPromotion(): Collection
    {
        return $this->Promotion;
    }

    public function setPromotion(Collection $Promotion): void
    {
        $this->Promotion = $Promotion;
    }
}
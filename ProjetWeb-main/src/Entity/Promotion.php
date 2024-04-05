<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('Promotion')]
class Promotion
{
    #[Id]
    #[Column, GeneratedValue]
    private int $Id_Promotion;

    #[Column]
    private string $Promotion;

    #[ManyToOne(inversedBy: 'Promotion')]
    #[JoinColumn(name: 'Center_Id',referencedColumnName: 'Id_Center')]
    private Center $Center;

    #[OneToMany(mappedBy: 'Promotion', targetEntity: Users::class)]
    private Collection $Users;

    public function __construct()
    {
        $this->Users = new ArrayCollection();
    }

    public function getIdPromotion(): int
    {
        return $this->Id_Promotion;
    }

    public function getPromotion(): string
    {
        return $this->Promotion;
    }

    public function setPromotion(string $Promotion): void
    {
        $this->Promotion = $Promotion;
    }

    public function getCenter(): Center
    {
        return $this->Center;
    }

    public function setCenter(Center $Center): void
    {
        $this->Center = $Center;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('Skill')]
class Skill
{
    #[Id]
    #[Column, GeneratedValue]
    private int $Id_Skill;

    #[Column]
    private string $Name_Skill;

    #[ManyToMany(targetEntity: Offer::class, mappedBy: "Offer_Skill")]
    private Collection $offers;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
    }

    public function getIdSkill(): int
    {
        return $this->Id_Skill;
    }

    public function getNameSkill(): string
    {
        return $this->Name_Skill;
    }

    public function setNameSkill(string $Name_Skill): void
    {
        $this->Name_Skill = $Name_Skill;
    }

    public function addOffer(Offer $offer): void
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            // Assurez-vous que la relation inverse est configurée dans Offer
            $offer->getOfferSkill()->add($this);
        }
    }

    public function removeOffer(Offer $offer): void
    {
        if ($this->offers->contains($offer)) {
            $this->offers->removeElement($offer);
            $offer->removeSkill($this);
        }
    }
}
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
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('Offer')]
class Offer
{
    #[Id, Column, GeneratedValue]
    private int $Id_Offer;

    #[Column]
    private string $Description;

    #[Column]
    private string $Promotion;

    #[Column]
    private string $Duration;

    #[Column(precision: 15, scale: 2)]
    private float $Remuneration;

    #[Column(type: Types::DATE_MUTABLE)]
    private \DateTime $Date_Offer;

    #[Column]
    private int $Places;

    #[Column]
    private bool $Del;

    #[ManyToOne(inversedBy: "Offer")]
    #[JoinColumn(name: 'Company_Id', referencedColumnName: 'Id_Company')]
    private Company $Company;

    #[ManyToOne(inversedBy: "Offer")]
    #[JoinColumn(name: 'Location_Id', referencedColumnName: 'Id_Location')]
    private Location $Location;

//    #[OneToMany(targetEntity: Evaluation::class, mappedBy: 'Offer')]
//    private Collection $Evaluation;

    #[ManyToMany(targetEntity: Users::class)]
    #[JoinTable(name: 'Wishlist')]
    #[JoinColumn(name: 'Offer_Id', referencedColumnName: 'Id_Offer')]
    #[InverseJoinColumn(name: 'Users_Id', referencedColumnName: 'Id_Users')]
    private Collection $Wishlist;

    #[ManyToMany(targetEntity: Skill::class)]
    #[JoinTable(name: 'Offer_Skill')]
    #[JoinColumn(name: 'Offer_Id', referencedColumnName: 'Id_Offer')]
    #[InverseJoinColumn(name: 'Skill_Id', referencedColumnName: 'Id_Skill')]
    private Collection $Offer_Skill;

    #[ManyToMany(targetEntity: Users::class)]
    #[JoinTable(name: 'Offer_Student')]
    #[JoinColumn(name: 'Offer_Id', referencedColumnName: 'Id_Offer')]
    #[InverseJoinColumn(name: 'Users_Id', referencedColumnName: 'Id_Users')]
    private Collection $Offer_Student;

    public function __construct()
    {
//        $this->Evaluation = new ArrayCollection();
        $this->Wishlist = new ArrayCollection();
        $this->Offer_Skill = new ArrayCollection();
        $this->Offer_Student = new ArrayCollection();
    }

    public function getIdOffer(): int
    {
        return $this->Id_Offer;
    }

    public function getPromotion(): string
    {
        return $this->Promotion;
    }

    public function setPromotion(string $Promotion): void
    {
        $this->Promotion = $Promotion;
    }

    public function getDuration(): string
    {
        return $this->Duration;
    }

    public function setDuration(string $Duration): void
    {
        $this->Duration = $Duration;
    }

    public function getRemuneration(): float
    {
        return $this->Remuneration;
    }

    public function setRemuneration(float $Remuneration): void
    {
        $this->Remuneration = $Remuneration;
    }

    public function getDateOffer(): \DateTime
    {
        return $this->Date_Offer;
    }

    public function setDateOffer(\DateTime $Date_Offer): void
    {
        $this->Date_Offer = $Date_Offer;
    }

    public function getPlaces(): int
    {
        return $this->Places;
    }

    public function setPlaces(int $Places): void
    {
        $this->Places = $Places;
    }

    public function isDel(): bool
    {
        return $this->Del;
    }

    public function setDel(bool $Del): void
    {
        $this->Del = $Del;
    }

    public function getDescription(): string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): void
    {
        $this->Description = $Description;
    }

    public function getCompany(): Company
    {
        return $this->Company;
    }

    public function setCompany(Company $Company): void
    {
        $this->Company = $Company;
    }

    public function getLocation(): Location
    {
        return $this->Location;
    }

    public function setLocation(Location $Location): void
    {
        $this->Location = $Location;
    }

    public function getOfferSkill(): Collection
    {
        return $this->Offer_Skill;
    }

    public function setOfferSkill(Collection $Offer_Skill): void
    {
        $this->Offer_Skill = $Offer_Skill;
    }

    public function addSkill(Skill $skill): void
    {
        if (!$this->Offer_Skill->contains($skill)) {
            $this->Offer_Skill->add($skill);
            $skill->addOffer($this);
        }
    }
    public function removeSkill(Skill $skill): void
    {
        if ($this->Offer_Skill->contains($skill)) {
            $this->Offer_Skill->removeElement($skill);
            $skill->removeOffer($this);
        }
    }

    public function addStudent(Users $user): void
    {
        if (!$this->Offer_Student->contains($user)) {
            $this->Offer_Student->add($user);
            $user->addOffer($this);
        }
    }
}

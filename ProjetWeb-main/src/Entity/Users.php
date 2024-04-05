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
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('Users')]
class Users
{
    #[Id,Column, GeneratedValue]
    private int $Id_Users;

    #[Column]
    private string $Type_Users;

    #[Column]
    private string $First_Name;

    #[Column]
    private string $Last_Name;

    #[Column]
    private string $Login;

    #[Column]
    private string $Password;

    #[Column (type: "string", length: 255, nullable: true)]
    private ?string $CV_Path = null;

    #[Column (type: "string", length: 255, nullable: true)]
    private ?string $Motivation_Path = null;

    #[Column(type: Types::BOOLEAN)]
    private bool $Del;

    #[ManyToOne(inversedBy: "Users")]
    #[JoinColumn(name: 'Promotion_Id',referencedColumnName: 'Id_Promotion')]
    private Promotion $Promotion;

//    #[OneToMany(targetEntity: Evaluation::class, mappedBy: 'Users')]
//    private Collection $Evaluation;

    #[ManyToMany(targetEntity: Offer::class, mappedBy: "Wishlist_Offer")]
    private Collection $Wishlist_Users;


    #[ManyToMany(targetEntity: Offer::class, mappedBy: "Offer_Student")]
    private Collection $Student_Offer;

    public function __construct()
    {
//        $this->Evaluation = new ArrayCollection();
        $this->Wishlist_Users = new ArrayCollection();
        $this->Student_Offer = new ArrayCollection();
    }

    public function getIdUsers(): int
    {
        return $this->Id_Users;
    }

    public function getTypeUsers(): string
    {
        return $this->Type_Users;
    }

    public function setTypeUsers(string $Type_Users): void
    {
        $this->Type_Users = $Type_Users;
    }

    public function getFirstName(): string
    {
        return $this->First_Name;
    }

    public function setFirstName(string $First_Name): void
    {
        $this->First_Name = $First_Name;
    }

    public function getLastName(): string
    {
        return $this->Last_Name;
    }

    public function setLastName(string $Last_Name): void
    {
        $this->Last_Name = $Last_Name;
    }

    public function getLogin(): string
    {
        return $this->Login;
    }

    public function setLogin(string $Login): void
    {
        $this->Login = $Login;
    }

    public function getPassword(): string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): void
    {
        $this->Password = $Password;
    }

    public function isDel(): bool
    {
        return $this->Del;
    }

    public function setDel(bool $Del): void
    {
        $this->Del = $Del;
    }

    public function getPromotion(): Promotion
    {
        return $this->Promotion;
    }

    public function setPromotion(Promotion $Promotion): void
    {
        $this->Promotion = $Promotion;
    }

    public function getCVPath(): ?string
    {
        return $this->CV_Path;
    }

    public function setCVPath(?string $CV_Path): void
    {
        $this->CV_Path = $CV_Path;
    }

    public function getMotivationPath(): ?string
    {
        return $this->Motivation_Path;
    }

    public function setMotivationPath(?string $Motivation_Path): void
    {
        $this->Motivation_Path = $Motivation_Path;
    }

    public function addOffer(Offer $offer): void
    {
        if (!$this->Student_Offer->contains($offer)) {
            $this->Student_Offer->add($offer);
            $offer->addStudent($this);
        }
    }
}
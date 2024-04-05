<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('Evaluation')]
class Evaluation
{
    #[Id]
    #[Column, GeneratedValue]
    private int $Id_Evaluation;

    #[Column(type: Types::FLOAT, precision: 15, scale: 2)]
    private float $Evaluation_Rate;

//    #[ManyToOne(inversedBy: 'Evaluation')]
//    #[JoinColumn(name: 'Users_Id',referencedColumnName: 'Id_Users')]
//    private Users $Users;

    #[ManyToOne(targetEntity: Company::class)]
    #[JoinColumn(name: "Company_Id", referencedColumnName: "Id_Company")]
    private Company $company;


    public function getIdEvaluation(): int
    {
        return $this->Id_Evaluation;
    }

    public function getEvaluationRate(): float
    {
        return $this->Evaluation_Rate;
    }

    public function setEvaluationRate(float $Evaluation_Rate): void
    {
        $this->Evaluation_Rate = $Evaluation_Rate;
    }

}
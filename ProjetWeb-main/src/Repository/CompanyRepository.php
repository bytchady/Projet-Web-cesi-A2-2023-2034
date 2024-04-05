<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CompanyRepository extends EntityRepository
{
    public function getPaginatedCompanies($page, $perPage = 6): array
    {
        $query = $this->createQueryBuilder("c")
            ->select("c")
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery();

        return $query->getResult();
    }
}

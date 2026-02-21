<?php

namespace App\Repository;

use App\Entity\Ranking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

// SECCIÓN: Repositorio de Rankings
class RankingRepository extends ServiceEntityRepository
{
    // SECCIÓN: Constructor
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ranking::class);
    }
}

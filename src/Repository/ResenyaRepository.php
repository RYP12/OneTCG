<?php

namespace App\Repository;

use App\Entity\Resenya;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

// SECCIÓN: Repositorio de Reseñas
class ResenyaRepository extends ServiceEntityRepository
{
    // SECCIÓN: Constructor
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resenya::class);
    }
}

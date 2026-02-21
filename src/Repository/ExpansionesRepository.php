<?php

namespace App\Repository;

use App\Entity\Expansiones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

// SECCIÓN: Repositorio de Expansiones
class ExpansionesRepository extends ServiceEntityRepository
{
    // SECCIÓN: Constructor
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expansiones::class);
    }
}

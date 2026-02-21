<?php

namespace App\Repository;

use App\Entity\Imagenes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

// SECCIÓN: Repositorio de Imágenes
class ImagenesRepository extends ServiceEntityRepository
{
    // SECCIÓN: Constructor
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Imagenes::class);
    }
}

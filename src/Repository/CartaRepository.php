<?php

namespace App\Repository;

use App\Entity\Carta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Carta>
 */
class CartaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Carta::class);
    }

    public function obtenerTodasLasCartas(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.imagenes', 'i')
            ->addSelect('i')
            ->leftJoin('c.expansion', 'e')
            ->addSelect('e')
            ->orderBy('c.expansion', 'DESC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }
}

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
            ->leftJoin('c.imagenes', 'i') // Unimos tablas para ahorrar consultas
            ->addSelect('i')              // Seleccionamos los datos de la imagen
            ->leftJoin('c.expansion', 'e')
            ->addSelect('e')
            // Ordenamos por expansión o ID para que tenga cierto orden visual
            ->orderBy('c.expansion', 'DESC')
            ->setMaxResults(100) // CRÍTICO: Sin esto, la web morirá con >2000 cartas
            ->getQuery()
            ->getResult();
    }

    //    /**
//     * @return Carta[] Returns an array of Carta objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?Carta
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

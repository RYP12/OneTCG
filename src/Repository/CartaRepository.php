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

    // SECCIÓN: Cartas Inicio
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

    // SECCIÓN: Cartas Directorio
    public function obtenerCartasDirectorio(?int $expansionId = null, ?string $nombre = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.imagenes', 'i')
            ->addSelect('i')
            ->leftJoin('c.expansion', 'e')
            ->addSelect('e')
            ->orderBy('e.nombreExpansion', 'ASC')
            ->addOrderBy('c.nombre', 'ASC');

        if ($expansionId !== null) {
            $qb->andWhere('e.id = :expansionId')
               ->setParameter('expansionId', $expansionId);
        }

        if ($nombre !== null && $nombre !== '') {
            $qb->andWhere('LOWER(c.nombre) LIKE LOWER(:nombre)')
               ->setParameter('nombre', '%' . $nombre . '%');
        }

        return $qb->getQuery()->getResult();
    }
}

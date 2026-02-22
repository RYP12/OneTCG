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

    // SECCIÓN: Cartas más valoradas
    public function obtenerCartasMasValoradas(int $limite = 10): array
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c AS carta, AVG(r.puntuacion) AS media, COUNT(r.id) AS num_resenyas
                 FROM App\Entity\Carta c
                 JOIN App\Entity\Resenya r WITH r.carta = c
                 GROUP BY c.id
                 ORDER BY media DESC'
            )
            ->setMaxResults($limite)
            ->getResult();
    }

    // SECCIÓN: Promedio de puntuaciones por ranking
    public function obtenerPromediosPorRanking(): array
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT rk AS ranking, AVG(r.puntuacion) AS media, COUNT(r.id) AS num_resenyas
                 FROM App\Entity\Ranking rk
                 LEFT JOIN rk.cartasDisponibles c
                 LEFT JOIN App\Entity\Resenya r WITH r.carta = c
                 GROUP BY rk.id
                 ORDER BY rk.nombre ASC'
            )
            ->getResult();
    }

    // SECCIÓN: Valoraciones recientes
    public function obtenerResenyasRecientes(int $limite = 10): array
    {
        return $this->findBy([], ['id' => 'DESC'], $limite);
    }

    // SECCIÓN: Total de valoraciones
    public function contarTotalResenyas(): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}

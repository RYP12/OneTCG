<?php

namespace App\Repository;

use App\Entity\ItemRanking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

// SECCIÓN: Repositorio de Items de Ranking
class ItemRankingRepository extends ServiceEntityRepository
{
    // SECCIÓN: Constructor
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemRanking::class);
    }

    // SECCIÓN: Promedio de posiciones por carta dentro de cada ranking
    // La puntuación de cada posición se calcula automáticamente:
    // posición 1 = 10 puntos, última posición = ~1 punto (escala normalizada 1-10)
    public function obtenerPromediosPorCartaEnRankings(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT
                r.id            AS ranking_id,
                r.nombre        AS ranking_nombre,
                c.id            AS carta_id,
                c.nombre        AS carta_nombre,
                ROUND(AVG(
                    (totales.total - ir.orden + 1)::float * 10.0 / totales.total
                )::numeric, 1) AS media,
                COUNT(ir.id)    AS num_votos
            FROM onetcg.item_ranking ir
            JOIN onetcg.ranking r       ON r.id = ir.ranking_id
            JOIN onetcg.carta c         ON c.id = ir.carta_id
            JOIN (
                SELECT ranking_id, usuario_id, COUNT(*) AS total
                FROM onetcg.item_ranking
                GROUP BY ranking_id, usuario_id
            ) totales ON totales.ranking_id = ir.ranking_id
                     AND totales.usuario_id  = ir.usuario_id
            GROUP BY r.id, r.nombre, c.id, c.nombre
            ORDER BY r.nombre ASC, media DESC
        ';

        $filas = $conn->fetchAllAssociative($sql);

        // SECCIÓN: Agrupar resultados por ranking
        $agrupado = [];
        foreach ($filas as $fila) {
            $id = $fila['ranking_id'];
            if (!isset($agrupado[$id])) {
                $agrupado[$id] = [
                    'ranking_id'     => $fila['ranking_id'],
                    'ranking_nombre' => $fila['ranking_nombre'],
                    'cartas'         => [],
                ];
            }
            $agrupado[$id]['cartas'][] = [
                'carta_id'     => $fila['carta_id'],
                'carta_nombre' => $fila['carta_nombre'],
                'media'        => $fila['media'],
                'num_votos'    => $fila['num_votos'],
            ];
        }

        return array_values($agrupado);
    }
}

<?php

namespace App\Controller;

use App\Entity\ItemRanking;
use App\Entity\Ranking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// SECCIÓN: Controlador de Mis Rankings
#[Route('/mis-rankings')]
class RankingController extends AbstractController
{
    // SECCIÓN: Índice Mis Rankings
    #[Route('/', name: 'app_mis_rankings')]
    public function index(EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();
        if (!$usuario)
            return $this->redirectToRoute('app_login');

        $todosLosRankings = $em->getRepository(Ranking::class)->findAll();
        $misItems = $em->getRepository(ItemRanking::class)->findBy(['usuario' => $usuario]);

        $idsParticipados = [];
        foreach ($misItems as $item) {
            $idsParticipados[] = $item->getRanking()->getId();
        }
        $idsParticipados = array_unique($idsParticipados);

        $rankingsParticipados = [];
        $rankingsNuevos = [];

        foreach ($todosLosRankings as $ranking) {
            if (in_array($ranking->getId(), $idsParticipados)) {
                $rankingsParticipados[] = $ranking;
            } else {
                $rankingsNuevos[] = $ranking;
            }
        }

        return $this->render('ranking/mis_rankings.html.twig', [
            'rankings_participados' => $rankingsParticipados,
            'rankings_nuevos' => $rankingsNuevos,
        ]);
    }

    // SECCIÓN: Iniciar Ranking Personal
    #[Route('/iniciar/{id}', name: 'app_ranking_iniciar')]
    public function iniciar(Ranking $ranking, EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();
        if (!$usuario)
            return $this->redirectToRoute('app_login');

        $existente = $em->getRepository(ItemRanking::class)->findOneBy(['usuario' => $usuario, 'ranking' => $ranking]);

        if (!$existente) {
            $orden = 1;
            foreach ($ranking->getCartasDisponibles() as $carta) {
                $item = new ItemRanking();
                $item->setRanking($ranking);
                $item->setUsuario($usuario);
                $item->setCarta($carta);
                $item->setOrden($orden);

                $em->persist($item);
                $orden++;
            }
            $em->flush();
        }

        return $this->redirectToRoute('app_ranking_detalle', ['id' => $ranking->getId()]);
    }

    // SECCIÓN: Detalle de Ranking Personal
    #[Route('/{id}', name: 'app_ranking_detalle')]
    public function detalle(Ranking $ranking, EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();
        if (!$usuario)
            return $this->redirectToRoute('app_login');

        $items = $em->getRepository(ItemRanking::class)->findBy(
            ['usuario' => $usuario, 'ranking' => $ranking],
            ['orden' => 'ASC']
        );

        return $this->render('ranking/detalle_ranking.html.twig', [
            'ranking' => $ranking,
            'items' => $items,
        ]);
    }

    // SECCIÓN: Mover Items
    #[Route('/{id}/mover/{item_id}/{direccion}', name: 'app_ranking_mover')]
    public function mover(Ranking $ranking, int $item_id, string $direccion, EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();
        if (!$usuario)
            return $this->redirectToRoute('app_login');

        $items = $em->getRepository(ItemRanking::class)->findBy(
            ['usuario' => $usuario, 'ranking' => $ranking],
            ['orden' => 'ASC']
        );

        $index = -1;
        foreach ($items as $k => $it) {
            if ($it->getId() === $item_id) {
                $index = $k;
                break;
            }
        }

        if ($index !== -1) {
            if ($direccion === 'arriba' && $index > 0) {
                $itemActual = $items[$index];
                $itemAnterior = $items[$index - 1];

                $ordenTemp = $itemActual->getOrden();
                $itemActual->setOrden($itemAnterior->getOrden());
                $itemAnterior->setOrden($ordenTemp);
                $em->flush();
            } elseif ($direccion === 'abajo' && $index < count($items) - 1) {
                $itemActual = $items[$index];
                $itemSiguiente = $items[$index + 1];

                $ordenTemp = $itemActual->getOrden();
                $itemActual->setOrden($itemSiguiente->getOrden());
                $itemSiguiente->setOrden($ordenTemp);
                $em->flush();
            }
        }

        return $this->redirectToRoute('app_ranking_detalle', ['id' => $ranking->getId()]);
    }
}
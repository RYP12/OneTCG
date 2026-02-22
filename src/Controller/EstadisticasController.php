<?php

namespace App\Controller;

use App\Repository\ItemRankingRepository;
use App\Repository\ResenyaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// SECCIÓN: Controlador de Estadísticas Globales
class EstadisticasController extends AbstractController
{
    // SECCIÓN: Página de Estadísticas
    #[Route('/estadisticas', name: 'app_estadisticas')]
    public function index(ResenyaRepository $resenyaRepository, ItemRankingRepository $itemRankingRepository): Response
    {
        $cartasMasValoradas      = $resenyaRepository->obtenerCartasMasValoradas(10);
        $totalResenyas           = $resenyaRepository->contarTotalResenyas();
        $opinionPorCartaRankings = $itemRankingRepository->obtenerPromediosPorCartaEnRankings();

        return $this->render('estadisticas/estadisticas.html.twig', [
            'cartas_mas_valoradas'       => $cartasMasValoradas,
            'total_resenyas'             => $totalResenyas,
            'opinion_por_carta_rankings' => $opinionPorCartaRankings,
        ]);
    }
}

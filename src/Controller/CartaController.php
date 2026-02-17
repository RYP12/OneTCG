<?php

namespace App\Controller;

use App\Repository\CartaRepository;
use App\Repository\ResenyaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartaController extends AbstractController
{
    #[Route('/', name: 'app_cartas')]
    public function cartasInicio(CartaRepository $cartaRepository): Response
    {
        $cartas = $cartaRepository->obtenerTodasLasCartas();

        return $this->render('home/home.html.twig', [
            'cartas' => $cartas,
        ]);
    }

    #[Route('/carta/{id}', name: 'app_carta_detalle')]
    public function cartaDetalle(CartaRepository $cartaRepository, ResenyaRepository $resenyaRepository, string $id): Response
    {
        $carta = $cartaRepository->find($id);

        if (!$carta) {
            throw $this->createNotFoundException('La carta no existe');
        }

        $resenyas = $resenyaRepository->findBy(['carta' => $carta], ['id' => 'DESC']);

        return $this->render('cartas/detalle.html.twig', [
            'carta' => $carta,
            'resenyas' => $resenyas,
        ]);
    }

}


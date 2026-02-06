<?php

namespace App\Controller;

use App\Repository\CartaRepository;
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
}


<?php

namespace App\Controller;

use App\Repository\CartaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CartaRepository $cartaRepository): Response
    {

        $cartas = $cartaRepository->obtenerTodasLasCartas();

        return $this->render('home/home.html.twig', [
            'cartas' => $cartas,
        ]);
    }
}

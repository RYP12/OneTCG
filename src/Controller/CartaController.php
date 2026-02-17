<?php

namespace App\Controller;

use App\Entity\Resenya;
use App\Repository\CartaRepository;
use App\Repository\ResenyaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// SECCIÓN: Controlador de Cartas
class CartaController extends AbstractController
{
    // SECCIÓN: Inicio de Cartas
    #[Route('/', name: 'app_cartas')]
    public function cartasInicio(CartaRepository $cartaRepository): Response
    {
        $cartas = $cartaRepository->obtenerTodasLasCartas();

        return $this->render('home/home.html.twig', [
            'cartas' => $cartas,
        ]);
    }

    // SECCIÓN: Detalle de Carta y Reseñas
    #[Route('/carta/{id}', name: 'app_carta_detalle')]
    public function cartaDetalle(Request $request, EntityManagerInterface $em, CartaRepository $cartaRepository, ResenyaRepository $resenyaRepository, string $id): Response
    {
        $carta = $cartaRepository->find($id);
        if (!$carta) {
            throw $this->createNotFoundException('La carta no existe');
        }

        if ($request->isMethod('POST') && $this->getUser()) {
            $usuarioActual = $this->getUser();

            $resenyaExistente = $resenyaRepository->findOneBy(['carta' => $carta, 'usuario' => $usuarioActual]);

            if ($resenyaExistente) {
                $resenyaExistente->setPuntuacion($request->request->get('puntuacion'));
                $resenyaExistente->setComentario($request->request->get('comentario'));
            } else {
                $nuevaResenya = new Resenya();
                $nuevaResenya->setCarta($carta);
                $nuevaResenya->setUsuario($usuarioActual);
                $nuevaResenya->setPuntuacion($request->request->get('puntuacion'));
                $nuevaResenya->setComentario($request->request->get('comentario'));
                $em->persist($nuevaResenya);
            }

            $em->flush();
            return $this->redirectToRoute('app_carta_detalle', ['id' => $id]);
        }

        $resenyas = $resenyaRepository->findBy(['carta' => $carta], ['id' => 'DESC']);

        return $this->render('cartas/detalle.html.twig', [
            'carta' => $carta,
            'resenyas' => $resenyas,
        ]);
    }
}
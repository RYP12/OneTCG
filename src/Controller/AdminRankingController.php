<?php

namespace App\Controller;

use App\Entity\Ranking;
use App\Form\RankingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// SECCIÓN: Controlador de Ranking Administrativo
#[Route('/admin/ranking')]
final class AdminRankingController extends AbstractController
{
    // SECCIÓN: Listado de Rankings
    #[Route(name: 'app_admin_ranking_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $rankings = $entityManager
            ->getRepository(Ranking::class)
            ->findAll();

        return $this->render('admin_ranking/index.html.twig', [
            'rankings' => $rankings,
        ]);
    }

    // SECCIÓN: Crear Nuevo Ranking
    #[Route('/new', name: 'app_admin_ranking_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ranking = new Ranking();
        $form = $this->createForm(RankingType::class, $ranking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ranking);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_ranking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ranking/new.html.twig', [
            'ranking' => $ranking,
            'form' => $form,
        ]);
    }

    // SECCIÓN: Mostrar Ranking
    #[Route('/{id}', name: 'app_admin_ranking_show', methods: ['GET'])]
    public function show(Ranking $ranking): Response
    {
        return $this->render('admin_ranking/show.html.twig', [
            'ranking' => $ranking,
        ]);
    }

    // SECCIÓN: Editar Ranking
    #[Route('/{id}/edit', name: 'app_admin_ranking_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ranking $ranking, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RankingType::class, $ranking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_ranking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ranking/edit.html.twig', [
            'ranking' => $ranking,
            'form' => $form,
        ]);
    }

    // SECCIÓN: Eliminar Ranking
    #[Route('/{id}', name: 'app_admin_ranking_delete', methods: ['POST'])]
    public function delete(Request $request, Ranking $ranking, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ranking->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ranking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_ranking_index', [], Response::HTTP_SEE_OTHER);
    }

    // SECCIÓN: Editar Cartas del Ranking
    #[Route('/admin/rankings/{id}/editar', name: 'admin_ranking_editar', methods: ['GET', 'POST'])]
    public function editarCartas(
        \Symfony\Component\HttpFoundation\Request $request,
        \App\Entity\Ranking $ranking,
        \Doctrine\ORM\EntityManagerInterface $em,
        \App\Repository\CartaRepository $cartaRepository
    ): \Symfony\Component\HttpFoundation\Response {

        if ($request->isMethod('POST')) {
            $ranking->getCartasDisponibles()->clear();

            $cartasSeleccionadas = $request->request->all('cartas');

            if (!empty($cartasSeleccionadas)) {
                foreach ($cartasSeleccionadas as $cartaId) {
                    $carta = $cartaRepository->find($cartaId);
                    if ($carta) {
                        $ranking->addCartasDisponible($carta);
                    }
                }
            }

            $em->flush();
            return $this->redirectToRoute('admin_rankings');
        }

        return $this->render('admin/ranking_editar.html.twig', [
            'ranking' => $ranking,
            'todas_las_cartas' => $cartaRepository->findAll(),
        ]);
    }
}

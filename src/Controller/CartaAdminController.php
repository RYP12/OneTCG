<?php

namespace App\Controller;

use App\Entity\Carta;
use App\Entity\Expansiones;
use App\Repository\CartaRepository;
use App\Repository\ExpansionesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// SECCIÓN: Controlador Admin de Cartas
#[Route('/admin/cartas')]
final class CartaAdminController extends AbstractController
{
    // SECCIÓN: Listado de Cartas
    #[Route('', name: 'admin_cartas_list', methods: ['GET'])]
    public function index(Request $request, CartaRepository $cartaRepository, ExpansionesRepository $expansionesRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $expansionId = $request->query->get('expansion') ? (int) $request->query->get('expansion') : null;
        $busqueda    = $request->query->get('q', '');

        $cartas     = $cartaRepository->obtenerCartasDirectorio($expansionId, $busqueda ?: null);
        $expansiones = $expansionesRepository->findBy([], ['nombreExpansion' => 'ASC']);

        return $this->render('admin/cartas/index.html.twig', [
            'cartas'          => $cartas,
            'expansiones'     => $expansiones,
            'expansionActiva' => $expansionId,
            'busqueda'        => $busqueda,
        ]);
    }

    // SECCIÓN: Crear Carta
    #[Route('/nueva', name: 'admin_carta_nueva', methods: ['GET', 'POST'])]
    public function nueva(Request $request, EntityManagerInterface $em, ExpansionesRepository $expansionesRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $expansiones = $expansionesRepository->findBy([], ['nombreExpansion' => 'ASC']);

        if ($request->isMethod('POST')) {
            $id = trim($request->request->get('id', ''));

            if ($id === '') {
                return $this->render('admin/cartas/nueva.html.twig', [
                    'expansiones' => $expansiones,
                    'error'       => 'El ID no puede estar vacío.',
                    'datos'       => $request->request->all(),
                ]);
            }

            $existente = $em->getRepository(Carta::class)->find($id);
            if ($existente) {
                return $this->render('admin/cartas/nueva.html.twig', [
                    'expansiones' => $expansiones,
                    'error'       => "Ya existe una carta con el ID «$id».",
                    'datos'       => $request->request->all(),
                ]);
            }

            $carta = new Carta();
            $carta->setId($id);
            $this->mapRequestToCarta($carta, $request, $em);

            $em->persist($carta);
            $em->flush();

            return $this->redirectToRoute('admin_cartas_list');
        }

        return $this->render('admin/cartas/nueva.html.twig', [
            'expansiones' => $expansiones,
            'error'       => null,
            'datos'       => [],
        ]);
    }

    // SECCIÓN: Editar Carta
    #[Route('/{id}/editar', name: 'admin_carta_editar', methods: ['GET', 'POST'])]
    public function editar(Request $request, string $id, EntityManagerInterface $em, CartaRepository $cartaRepository, ExpansionesRepository $expansionesRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $carta = $cartaRepository->find($id);
        if (!$carta) {
            throw $this->createNotFoundException("La carta «$id» no existe.");
        }

        $expansiones = $expansionesRepository->findBy([], ['nombreExpansion' => 'ASC']);

        if ($request->isMethod('POST')) {
            $this->mapRequestToCarta($carta, $request, $em);
            $em->flush();
            return $this->redirectToRoute('admin_cartas_list');
        }

        return $this->render('admin/cartas/editar.html.twig', [
            'carta'       => $carta,
            'expansiones' => $expansiones,
        ]);
    }

    // SECCIÓN: Eliminar Carta
    #[Route('/{id}/eliminar', name: 'admin_carta_eliminar', methods: ['GET'])]
    public function eliminar(string $id, EntityManagerInterface $em, CartaRepository $cartaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $carta = $cartaRepository->find($id);
        if ($carta) {
            $em->remove($carta);
            $em->flush();
        }

        return $this->redirectToRoute('admin_cartas_list');
    }

    // SECCIÓN: Helper — Mapear Request a Carta
    private function mapRequestToCarta(Carta $carta, Request $request, EntityManagerInterface $em): void
    {
        $carta->setNombre(trim($request->request->get('nombre', '')));
        $carta->setTipo(trim($request->request->get('tipo', '')));
        $carta->setRarity($request->request->get('rarity') ?: null);
        $carta->setColor($request->request->get('color') ?: null);
        $carta->setCoste($request->request->get('coste') !== '' ? (int) $request->request->get('coste') : null);
        $carta->setPoder($request->request->get('poder') !== '' ? (int) $request->request->get('poder') : null);
        $carta->setCounter($request->request->get('counter') !== '' ? (int) $request->request->get('counter') : null);
        $carta->setFamilia($request->request->get('familia') ?: null);
        $carta->setHabilidad($request->request->get('habilidad') ?: null);
        $carta->setEfectoTrigger($request->request->get('efectoTrigger') ?: null);
        $carta->setAtributoNombre($request->request->get('atributoNombre') ?: null);

        $expansionId = $request->request->get('expansion');
        $expansion   = $expansionId ? $em->getRepository(Expansiones::class)->find((int) $expansionId) : null;
        $carta->setExpansion($expansion);
    }
}

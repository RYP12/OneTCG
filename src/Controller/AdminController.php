<?php

namespace App\Controller;

use App\Entity\Carta;
use App\Entity\Expansiones;
use App\Entity\Imagenes;
use App\Entity\Ranking;
use App\Entity\Resenya;
use App\Entity\Usuario;
use App\Repository\CartaRepository;
use App\Repository\ResenyaRepository;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;

// SECCIÓN: Controlador de Administración
final class AdminController extends AbstractController
{
    // SECCIÓN: Rutas Principales
    #[Route('/admin', name: 'admin_site')]
    public function index(EntityManagerInterface $em, UsuarioRepository $usuarioRepository, ResenyaRepository $resenyaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $usuarios         = $usuarioRepository->findBy([], ['id' => 'DESC']);
        $rankings         = $em->getRepository(Ranking::class)->findBy([], ['nombre' => 'ASC']);
        $resenyasRecientes = $resenyaRepository->obtenerResenyasRecientes(10);

        return $this->render('admin/admin.html.twig', array_merge(
            $this->buildStats($em),
            [
                'lista_usuarios'     => $usuarios,
                'lista_rankings'     => $rankings,
                'resenyas_recientes' => $resenyasRecientes,
            ]
        ));
    }

    private function buildStats(EntityManagerInterface $em): array
    {
        return [
            'total_cartas'      => $em->getRepository(Carta::class)->count([]),
            'total_expansiones' => $em->getRepository(Expansiones::class)->count([]),
            'total_usuarios'    => $em->getRepository(Usuario::class)->count([]),
            'total_rankings'    => $em->getRepository(Ranking::class)->count([]),
            'total_resenyas'    => $em->getRepository(Resenya::class)->count([]),
        ];
    }

    #[Route('/admin/rankings', name: 'admin_rankings')]
    public function gestionRankings(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $nombre = trim($request->request->get('nombre', ''));
            if ($nombre !== '') {
                $ranking = new Ranking();
                $ranking->setNombre($nombre);
                $em->persist($ranking);
                $em->flush();
            }
            return $this->redirectToRoute('admin_rankings');
        }

        $rankings = $em->getRepository(Ranking::class)->findAll();

        return $this->render('admin/generalAdminRankings.html.twig', [
            'rankings' => $rankings,
        ]);
    }

    // SECCIÓN: Gestión de Cartas en Tierlist
    #[Route('/admin/rankings/{id}/editar', name: 'admin_ranking_editar', methods: ['GET', 'POST'])]
    public function editarCartas(Request $request, Ranking $ranking, EntityManagerInterface $em, CartaRepository $cartaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            foreach ($ranking->getCartasDisponibles() as $cartaExistente) {
                $ranking->removeCartasDisponible($cartaExistente);
            }

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

        return $this->render('admin/gestionRanking.html.twig', [
            'ranking' => $ranking,
            'todas_las_cartas' => $cartaRepository->findAll(),
        ]);
    }

    #[Route('/admin/rankings/{id}/eliminar', name: 'admin_ranking_eliminar')]
    public function eliminarRanking(Ranking $ranking, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($ranking);
        $em->flush();

        return $this->redirectToRoute('admin_rankings');
    }

    // SECCIÓN: Carga de Datos API
    #[Route('/admin/cartas/load', name: 'data_load_api')]
    public function data_load(HttpClientInterface $httpClient, EntityManagerInterface $entityManager, ?Profiler $profiler = null): Response
    {
        if (null !== $profiler) {
            $profiler->disable();
        }
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $apiKey = '67a3838ecdd55f9ba1eb49abe1cb84a595d7fc6195a407de7534bf3940dc1871';
        $page = 1;
        $totalProcessed = 0;

        while (true) {
            $response = $httpClient->request(
                'GET',
                "https://apitcg.com/api/one-piece/cards?page=$page&limit=100",
                [
                    'headers' => [
                        'x-api-key' => $apiKey,
                        'Accept' => 'application/json',
                    ],
                ]
            );

            $data = $response->toArray();
            $content = $data['data'] ?? $data;

            if (empty($content)) {
                break;
            }

            foreach ($content as $element) {
                $setName = $element['set']['name'] ?? 'Generic Set';
                $expansion = $entityManager->getRepository(Expansiones::class)->findOneBy(['nombreExpansion' => $setName]);

                if (!$expansion) {
                    $expansion = new Expansiones();
                    $expansion->setNombreExpansion($setName);
                    $entityManager->persist($expansion);
                    $entityManager->flush();
                }

                $carta = $entityManager->getRepository(Carta::class)->find($element['id']);
                if (!$carta) {
                    $carta = new Carta();
                    $carta->setId($element['id']);
                }

                $carta->setNombre($element['name']);
                $carta->setRarity($element['rarity'] ?? null);
                $carta->setTipo($element['type']);
                $carta->setCoste($element['cost'] ?? null);
                $carta->setAtributoNombre($element['attribute']['name'] ?? null);
                $carta->setAtributoIconoUrl($element['attribute']['image'] ?? null);
                $carta->setPoder($element['power'] ?? null);
                $carta->setCounter((int) ($element['counter'] ?? 0));
                $carta->setColor($element['color'] ?? null);
                $carta->setFamilia($element['family'] ?? null);
                $carta->setHabilidad($element['ability'] ?? null);
                $carta->setEfectoTrigger($element['trigger'] ?? null);
                $carta->setExpansion($expansion);

                $entityManager->persist($carta);

                if ($carta->getId()) {
                    $existingImages = $entityManager->getRepository(Imagenes::class)->findBy(['carta' => $carta]);
                    foreach ($existingImages as $oldImg) {
                        $entityManager->remove($oldImg);
                    }
                }

                if (isset($element['images']['large'])) {
                    $imgLarge = new Imagenes();
                    $imgLarge->setCarta($carta);
                    $imgLarge->setTamanyo('large');
                    $imgLarge->setUrl($element['images']['large']);
                    $entityManager->persist($imgLarge);
                }

                if (isset($element['images']['small'])) {
                    $imgSmall = new Imagenes();
                    $imgSmall->setCarta($carta);
                    $imgSmall->setTamanyo('small');
                    $imgSmall->setUrl($element['images']['small']);
                    $entityManager->persist($imgSmall);
                }

                $totalProcessed++;
            }

            $entityManager->flush();
            $entityManager->clear();

            $page++;
            if ($page > 150)
                break;
        }

        return $this->render('admin/admin.html.twig', array_merge(
            $this->buildStats($entityManager),
            [
                'status'  => 'success',
                'message' => "Se han cargado/actualizado $totalProcessed cartas correctamente.",
            ]
        ));
    }
}

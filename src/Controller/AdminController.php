<?php

namespace App\Controller;

use App\Entity\Carta;
use App\Entity\Expansiones;
use App\Entity\Imagenes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_site')]
    public function index(): Response
    {
        return $this->render('admin/admin.html.twig');
    }

    #[Route('/admin/cartas/load', name: 'data_load_api')]
    public function data_load(
        HttpClientInterface $httpClient,
        EntityManagerInterface $entityManager,
        ?Profiler $profiler = null
    ): Response {
        // Ajustar la memoria para poder mejorar el redimiento de los 3000 articulos
        if (null !== $profiler) {
            $profiler->disable(); // Desactiva el debug para ahorrar RAM
        }
        ini_set('memory_limit', '512M');
        set_time_limit(0); // Tiempo de ejecución ilimitado

        // Mi api key (cuenta personal creada)
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

            // Si la página está vacía, hemos terminado
            if (empty($content)) {
                break;
            }

            foreach ($content as $element) {
                // Expansión
                $setName = $element['set']['name'] ?? 'Generic Set';
                $expansion = $entityManager->getRepository(Expansiones::class)->findOneBy(['nombreExpansion' => $setName]);

                if (!$expansion) {
                    $expansion = new Expansiones();
                    $expansion->setNombreExpansion($setName);
                    $entityManager->persist($expansion);
                    $entityManager->flush(); // Necesario para obtener ID antes de asignar a carta
                }

                // Buscar por id string de la Aapi
                $carta = $entityManager->getRepository(Carta::class)->find($element['id']);
                if (!$carta) {
                    $carta = new Carta();
                    $carta->setId($element['id']);
                }

                // Mapeo de campos y con posibilidad de nulos
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

                // En caso de existir imagenes para no saturar ni duplicar se eliminan las anteriores y se añaden nuevas
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

            // Liberamos memoria para no saturar el proceso de extraccion
            $entityManager->flush();
            $entityManager->clear(); // Libera la RAM de los objetos procesados

            $page++;
            if ($page > 150)
                break; // Límite de seguridad por si la API entra en bucle
        }

        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
            'status' => 'success',
            'message' => "Se han cargado/actualizado $totalProcessed cartas correctamente.",
            'content' => [] // No enviamos miles de objetos a Twig para evitar error de memoria en la vista
        ]);
    }
}

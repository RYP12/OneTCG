<?php

namespace App\Controller;

use App\Entity\Expansiones;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// SECCIÓN: Controlador Admin de Expansiones
#[Route('/admin/expansiones')]
final class ExpansionesAdminController extends AbstractController
{
    // SECCIÓN: Listado de Expansiones
    #[Route('', name: 'admin_expansiones', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $nombre = trim($request->request->get('nombre', ''));
            if ($nombre !== '') {
                $existente = $em->getRepository(Expansiones::class)->findOneBy(['nombreExpansion' => $nombre]);
                if (!$existente) {
                    $expansion = new Expansiones();
                    $expansion->setNombreExpansion($nombre);
                    $em->persist($expansion);
                    $em->flush();
                }
            }
            return $this->redirectToRoute('admin_expansiones');
        }

        $expansionesRaw = $em->createQuery(
            'SELECT e, COUNT(c.id) AS num_cartas
             FROM App\Entity\Expansiones e
             LEFT JOIN App\Entity\Carta c WITH c.expansion = e
             GROUP BY e.id
             ORDER BY e.nombreExpansion ASC'
        )->getResult();

        $expansiones = [];
        foreach ($expansionesRaw as $row) {
            $expansiones[] = [
                'expansion'  => $row[0],
                'num_cartas' => $row['num_cartas'],
            ];
        }

        return $this->render('admin/expansiones/index.html.twig', [
            'expansiones' => $expansiones,
        ]);
    }

    // SECCIÓN: Editar Expansión
    #[Route('/{id}/editar', name: 'admin_expansion_editar', methods: ['GET', 'POST'])]
    public function editar(Request $request, Expansiones $expansion, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $nombre = trim($request->request->get('nombre', ''));
            if ($nombre !== '') {
                $expansion->setNombreExpansion($nombre);
                $em->flush();
            }
            return $this->redirectToRoute('admin_expansiones');
        }

        return $this->render('admin/expansiones/editar.html.twig', [
            'expansion' => $expansion,
        ]);
    }

    // SECCIÓN: Eliminar Expansión
    #[Route('/{id}/eliminar', name: 'admin_expansion_eliminar', methods: ['GET'])]
    public function eliminar(Expansiones $expansion, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($expansion);
        $em->flush();

        return $this->redirectToRoute('admin_expansiones');
    }
}

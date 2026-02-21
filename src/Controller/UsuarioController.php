<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

// SECCIÓN: Controlador de Usuario
final class UsuarioController extends AbstractController
{
    // SECCIÓN: Registro de Usuario
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $newUser = new Usuario();

            $newUser->setUsername($request->request->get('username'));
            $newUser->setEmail($request->request->get('email'));

            $passwordText = $request->request->get('password');

            $hashedPassword = $passwordHasher->hashPassword($newUser, $passwordText);
            $newUser->setContrasenya($hashedPassword);

            $newUser->setRoles(['ROLE_USER']);

            $entityManager->persist($newUser);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('usuario/register.html.twig');
    }
}

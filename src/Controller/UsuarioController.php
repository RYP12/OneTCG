<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // <--- Faltaba esto
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class UsuarioController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // Solo entramos aquí si el usuario ha pulsado el botón "Save"
        if ($request->isMethod('POST')) {
            $newUser = new Usuario();

            // Recogemos los datos de los inputs HTML (usando los 'name' del HTML)
            $newUser->setUsername($request->request->get('username'));
            $newUser->setEmail($request->request->get('email')); // Ojo: en tu HTML pusiste name="mail", no "email"

            $passwordText = $request->request->get('password');

            // Encriptamos la contraseña
            $hashedPassword = $passwordHasher->hashPassword($newUser, $passwordText);
            $newUser->setContrasenya($hashedPassword);

            // Asignamos rol por defecto
            $newUser->setRoles(['ROLE_USER']);

            // Guardamos en la base de datos
            $entityManager->persist($newUser);
            $entityManager->flush();

            // ¡Importante! Si todo sale bien, nos vamos al login
            return $this->redirectToRoute('app_login');
        }

        // Si entramos por primera vez (GET), simplemente mostramos el formulario
        return $this->render('usuario/register.html.twig');
    }

}

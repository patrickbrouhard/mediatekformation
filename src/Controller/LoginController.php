<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(['/login'], name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // récupération éventuelle de l'erreur + login
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        // Juste pour créer la route
    }

    #[Route('/admin', name: 'admin')]
    public function loginRedirect()
    {
        return $this->redirectToRoute('admin.formations');
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur de gestion de l'authentification.
 */
class LoginController extends AbstractController
{
    /**
     * Affiche la page de connexion et gère les erreurs d'authentification.
     *
     * @param AuthenticationUtils $authenticationUtils Utilitaire d'authentification
     * @return Response Réponse HTTP
     */
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

    /**
     * Route de déconnexion (interceptée par Symfony).
     *
     * @return void
     */
    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        // Juste pour créer la route
    }

    /**
     * Redirige vers la page d'administration après connexion.
     *
     * @return Response Réponse HTTP
     */
    #[Route('/admin', name: 'admin')]
    public function loginRedirect()
    {
        return $this->redirectToRoute('admin.formations');
    }
}

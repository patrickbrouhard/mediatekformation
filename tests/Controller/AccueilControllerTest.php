<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests fonctionnels du contrôleur AccueilController
 */
class AccueilControllerTest extends WebTestCase
{
    /**
     * Vérifie que la page d'accueil est accessible
     * et retourne un code HTTP 200 (succès).
     *
     * Objectif :
     * - simuler une requête HTTP GET vers "/"
     * - contrôler que la réponse est correcte
     */
    public function testAccesPage(): void
    {
        // Création d'un client HTTP simulé
        $client = static::createClient();

        // Simulation d'une requête GET vers la page d'accueil
        $client->request('GET', '/');

        // Vérifie que la réponse HTTP est 200 (page accessible)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}

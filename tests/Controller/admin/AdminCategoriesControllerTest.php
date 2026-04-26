<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests fonctionnels du contrôleur d'administration des catégories.
 *
 * Vérifie :
 * - l'accès à la page d'administration des catégories
 * - l'affichage du formulaire d'ajout
 * - la présence du bouton de suppression
 * - la gestion des suppressions impossibles
 * - la gestion des catégories inexistantes
 */
class AdminCategoriesControllerTest extends WebTestCase
{
    /**
     * Route racine de la gestion des catégories en administration.
     *
     * @var string
     */
    private const RACINE = '/admin/categories';

    /**
     * Crée un client HTTP authentifié avec un utilisateur administrateur.
     *
     * @return \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private function createClientLoggedIn()
    {
        $client = static::createClient();

        $user = self::getContainer()
            ->get(UserRepository::class)
            ->findOneBy(['username' => 'admin']);

        $this->assertNotNull(
            $user,
            'Utilisateur admin introuvable dans la base de test'
        );

        $client->loginUser($user);

        return $client;
    }

    /**
     * Vérifie l'accès à la page d'administration des catégories.
     *
     * @return void
     */
    public function testAccesPageAdminCategories(): void
    {
        $client = $this->createClientLoggedIn();
        $client->request('GET', self::RACINE);
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK
        );
    }

    /**
     * Vérifie la présence du mini-formulaire d'ajout de catégorie.
     *
     * @return void
     */
    public function testAffichageFormAjoutCategorie(): void
    {
        $client = $this->createClientLoggedIn();
        $client->request('GET', self::RACINE);
        $this->assertSelectorExists('form');
    }

    /**
     * Vérifie la présence du bouton de suppression d'une catégorie.
     *
     * @return void
     */
    public function testBoutonSuppressionCategorieExiste(): void
    {
        $client = $this->createClientLoggedIn();
        $client->request('GET', self::RACINE);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists(
            'a.btn-danger'
        );
    }

    /**
     * Vérifie la tentative de suppression d'une catégorie liée à des formations.
     *
     * @return void
     */
    public function testSuppressionCategorieAvecFormations(): void
    {
        $client = $this->createClientLoggedIn();

        $crawler = $client->request(
            'GET',
            self::RACINE
        );

        $this->assertResponseIsSuccessful();

        $lien = $crawler
            ->filter('tbody tr:first-child a.btn-danger')
            ->link();

        $client->click($lien);

        $this->assertResponseRedirects(
            self::RACINE
        );

        $client->followRedirect();

        $this->assertSelectorTextContains(
            '.alert-danger',
            'Impossible de supprimer une catégorie'
        );
    }

    /**
     * Vérifie la tentative de suppression d'une catégorie inexistante.
     *
     * @return void
     */
    public function testSuppressionCategorieInexistante(): void
    {
        $client = $this->createClientLoggedIn();

        $client->request(
            'GET',
            '/admin/categorie/suppr/999999'
        );

        $this->assertResponseStatusCodeSame(
            Response::HTTP_NOT_FOUND
        );
    }
}

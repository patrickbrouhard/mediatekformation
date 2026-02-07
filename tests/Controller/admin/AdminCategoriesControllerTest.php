<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminCategoriesControllerTest extends WebTestCase
{
    private const RACINE = '/admin/categories';

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
     * Vérifie accès page catégories admin
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
     * Vérifie la présence du mini-formulaire d'ajout
     */
    public function testAffichageFormAjoutCategorie(): void
    {
        $client = $this->createClientLoggedIn();
        $client->request('GET', self::RACINE);
        $this->assertSelectorExists('form');
    }

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
     * Vérifie suppression catégorie liée à formations
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
     * Vérifie suppression catégorie inexistante
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
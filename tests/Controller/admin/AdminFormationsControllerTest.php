<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests fonctionnels du contrôleur AdminFormationsController
 */
class AdminFormationsControllerTest extends WebTestCase
{
    private const RACINE = '/admin/formations';
    private const SELECTEUR_TITRE_PREMIERE_LIGNE =
        'tbody tr:first-child td:first-child';
    private const SELECTEUR_TITRES_TABLEAU =
        'tbody tr td:first-child';
    private const TITRE_ATTENDU_CSHARP =
        'Bases de la programmation n°74 - POO : collections';

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
     * Vérifie que la page admin formations est accessible
     */
    public function testAccesPageAdminFormations(): void
    {
        $client = $this->createClientLoggedIn();
        $client->request('GET', self::RACINE);
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK
        );
    }


    /**
     * Vérifie que le clic sur le bouton éditer
     * permet d'accéder à la page d'édition
     */
    public function testClicEditFormation(): void
    {
        $client = $this->createClientLoggedIn();
        $crawler = $client->request('GET',self::RACINE);
        $this->assertResponseIsSuccessful();

        $lien = $crawler
            ->filter('tbody tr:first-child a.btn-outline-secondary')
            ->link();

        $client->click($lien);

        $this->assertResponseIsSuccessful();
        
        $this->assertRouteSame(
            'admin.formation.edit',
            ['id' => 1]
        );
    }

    /**
     * Vérifie que la suppression redirige correctement
     */
    public function testSuppressionFormation(): void
    {
        $client = $this->createClientLoggedIn();
        $crawler = $client->request(
            'GET',
            self::RACINE
        );

        $this->assertResponseIsSuccessful();
        $lien = $crawler
            ->filter('tbody tr:first-child a.btn-outline-danger')
            ->link();

        $this->assertSelectorExists(
            'a.btn-outline-danger'
        );
    }
    
    /**
     * Test générique des tris admin formations
     *
     * @dataProvider providerTriFormations
     */
    public function testTriFormations(
        string $url,
        string $titreAttendu
    ): void
    {
        $client = $this->createClientLoggedIn();

        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            self::SELECTEUR_TITRE_PREMIERE_LIGNE,
            $titreAttendu
        );
    }


    /**
     * Fournit les cas de test des tris admin formations
     */
    public function providerTriFormations(): array
    {
        return [

            "tri titre ASC" => [
                '/admin/formations/tri/title/asc',
                'Android Studio (complément n°1) : Navigation Drawer et Fragment'
            ],

            "tri titre DESC" => [
                '/admin/formations/tri/title/desc',
                'UML : Diagramme de paquetages'
            ],

            "tri playlist ASC" => [
                '/admin/formations/tri/name/asc/playlist',
                self::TITRE_ATTENDU_CSHARP
            ],

            "tri playlist DESC" => [
                '/admin/formations/tri/name/desc/playlist',
                'C# : ListBox en couleur'
            ],

            "tri date ASC" => [
                '/admin/formations/tri/publishedAt/asc',
                "Cours UML (1 à 7 / 33) : introduction et cas d'utilisation"
            ],

            "tri date DESC" => [
                '/admin/formations/tri/publishedAt/desc',
                'Eclipse n°8 : Déploiement'
            ],

        ];
    }

    /**
     * Vérifie que les filtres texte fonctionnent
     *
     * @dataProvider providerFiltresTexte
     */
    public function testFiltresTexte(
        string $valeurRecherche,
        int $nbResultatsAttendus,
        string $titreAttendu
    ): void
    {
        $client = $this->createClientLoggedIn();

        $client->request('GET', self::RACINE);

        $crawler = $client->submitForm(
            'Filtrer',
            ['recherche' => $valeurRecherche]
        );

        $this->assertCount(
            $nbResultatsAttendus,
            $crawler->filter(
                self::SELECTEUR_TITRES_TABLEAU
            )
        );

        $this->assertSelectorTextContains(
            self::SELECTEUR_TITRE_PREMIERE_LIGNE,
            $titreAttendu
        );
    }


    /**
     * Fournit les cas de test des filtres texte
     */
    public function providerFiltresTexte(): array
    {
        return [

            "filtre nom exact" => [
                'UML : Diagramme de paquetages',
                1,
                'UML : Diagramme de paquetages'
            ],

            "filtre playlist" => [
                'uml',
                10,
                'UML : Diagramme de paquetages'
            ],

        ];
    }


    /**
     * Vérifie que le filtre par catégorie fonctionne
     */
    public function testFiltreParCategorie(): void
    {
        $client = $this->createClientLoggedIn();

        $crawler = $client->request(
            'GET',
            self::RACINE
        );

        $this->assertResponseIsSuccessful();

        $form = $crawler
            ->filter('form[action*="categories"]')
            ->form();

        $form['recherche'] = '2';

        $crawler = $client->submit($form);

        $this->assertCount(
            11,
            $crawler->filter(
                self::SELECTEUR_TITRES_TABLEAU
            )
        );

        $this->assertSelectorTextContains(
            self::SELECTEUR_TITRE_PREMIERE_LIGNE,
            'Eclipse n°2 : rétroconception avec ObjectAid'
        );
    }


    /**
     * Vérifie accès page ajout formation
     */
    public function testAccesPageAjoutFormation(): void
    {
        $client = $this->createClientLoggedIn();

        $client->request(
            'GET',
            '/admin/formation/ajout'
        );

        $this->assertResponseIsSuccessful();
    }
}

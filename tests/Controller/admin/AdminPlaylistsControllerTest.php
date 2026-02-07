<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminPlaylistsControllerTest extends WebTestCase
{
    private const RACINE = '/admin/playlists';
    private const SELECTEUR_TITRE_PREMIERE_LIGNE =
        'tbody tr:first-child td:first-child';
    private const SELECTEUR_TITRES_TABLEAU =
        'tbody tr td:first-child';
    private const TITRE_ATTENDU_CSHARP =
        'Bases de la programmation (C#)';


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
     * Vérifie que la page admin playlists est accessible
     */
    public function testAccesPageAdminPlaylists(): void
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
    public function testClicEditPlaylist(): void
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
            'admin.playlist.edit',
            ['id' => 1]
        );
    }

    public function testBoutonSuppressionDisabled(): void
    {
        $client = $this->createClientLoggedIn();
        $crawler = $client->request('GET',self::RACINE);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists(
            'a.btn-outline-danger.disabled'
        );
    }


    /**
     * @dataProvider providerTriPlaylists
     */
    public function testTriPlaylists(
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


    public function providerTriPlaylists(): array
    {
        return [

            "tri nom ASC" => [
                '/admin/playlists/tri/name/asc',
                self::TITRE_ATTENDU_CSHARP
            ],

            "tri nom DESC" => [
                '/admin/playlists/tri/name/desc',
                'Visual Studio 2019 et C#'
            ],

            "tri nb formations ASC" => [
                '/admin/playlists/tri/nbFormations/asc',
                'Cours de programmation objet'
            ],

            "tri nb formations DESC" => [
                '/admin/playlists/tri/nbFormations/desc',
                self::TITRE_ATTENDU_CSHARP
            ],

        ];
    }


    /**
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


    public function providerFiltresTexte(): array
    {
        return [

            "filtre nom exact" => [
                'programmation objet',
                1,
                'Cours de programmation objet'
            ],

        ];
    }


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

        $this->assertGreaterThan(
            0,
            $crawler->filter(
                self::SELECTEUR_TITRES_TABLEAU
            )->count()
        );
    }


    public function testAccesPageAjoutPlaylist(): void
    {
        $client = $this->createClientLoggedIn();

        $client->request(
            'GET',
            '/admin/playlist/ajout'
        );

        $this->assertResponseIsSuccessful();
    }
}
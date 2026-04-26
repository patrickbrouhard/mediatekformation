<?php

namespace App\Tests\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests fonctionnels du contrôleur d'administration des playlists.
 *
 * Vérifie :
 * - l'accès à la page d'administration
 * - l'accès à l'édition d'une playlist
 * - l'état du bouton de suppression
 * - le tri des playlists
 * - le filtrage texte
 * - le filtrage par catégorie
 * - l'accès à la page d'ajout d'une playlist
 */
class AdminPlaylistsControllerTest extends WebTestCase
{
    /**
     * Route racine de la gestion des playlists en administration.
     *
     * @var string
     */
    private const RACINE = '/admin/playlists';

    /**
     * Sélecteur CSS du titre de la première ligne du tableau.
     *
     * @var string
     */
    private const SELECTEUR_TITRE_PREMIERE_LIGNE =
        'tbody tr:first-child td:first-child';

    /**
     * Sélecteur CSS des titres présents dans le tableau.
     *
     * @var string
     */
    private const SELECTEUR_TITRES_TABLEAU =
        'tbody tr td:first-child';

    /**
     * Titre attendu pour certaines vérifications de tri.
     *
     * @var string
     */
    private const TITRE_ATTENDU_CSHARP =
        'Bases de la programmation (C#)';


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
     * Vérifie que la page d'administration des playlists est accessible.
     *
     * @return void
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
     * permet d'accéder à la page d'édition.
     *
     * @return void
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

    /**
     * Vérifie que le bouton de suppression est désactivé lorsque nécessaire.
     *
     * @return void
     */
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
     * Vérifie le tri des playlists selon différents critères.
     *
     * @dataProvider providerTriPlaylists
     *
     * @param string $url URL de tri appelée
     * @param string $titreAttendu Titre attendu en première position
     * @return void
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


    /**
     * Fournit les cas de test pour le tri des playlists.
     *
     * @return array<string, array{string,string}>
     */
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
     * Vérifie le filtrage texte des playlists.
     *
     * @dataProvider providerFiltresTexte
     *
     * @param string $valeurRecherche Valeur recherchée
     * @param int $nbResultatsAttendus Nombre de résultats attendus
     * @param string $titreAttendu Premier titre attendu
     * @return void
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
     * Fournit les cas de test pour le filtrage texte des playlists.
     *
     * @return array<string, array{string,int,string}>
     */
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


    /**
     * Vérifie le filtrage des playlists par catégorie.
     *
     * @return void
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

        $this->assertGreaterThan(
            0,
            $crawler->filter(
                self::SELECTEUR_TITRES_TABLEAU
            )->count()
        );
    }


    /**
     * Vérifie l'accès à la page d'ajout d'une playlist.
     *
     * @return void
     */
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

<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests fonctionnels du contrôleur PlaylistsController
 */
class PlaylistsControllerTest extends WebTestCase
{
    private const RACINE = '/playlists';
    private const TITRE_ATTENDU_CSHARP = 'Bases de la programmation (C#)';

    private const SELECTEUR_TITRE_PREMIERE_LIGNE =
        'tbody tr:first-child h5';

    private const SELECTEUR_TITRES_TABLEAU =
        'tbody tr h5';

    /**
     * Vérifie que la page des playlists est accessible
     */
    public function testAccesPagePlaylists(): void
    {
        $client = static::createClient();
        $client->request('GET', self::RACINE);
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK
        );
    }

    /**
     * Vérifie que le clic sur le bouton "Voir détail"
     * de la première playlist permet d'accéder à la page détail
     */
    public function testClicAccessPlaylist(): void
    {
        $client = static::createClient();
        $crawler = $client->request(
            'GET',
            self::RACINE
        );

        $this->assertResponseIsSuccessful();

        // Récupération du lien "Voir détail"
        $lien = $crawler
            ->filter(
                'tbody tr:first-child td:last-child a'
            )
            ->link();

        // Simulation clic utilisateur
        $client->click($lien);

        // Vérifie accès page détail
        $this->assertResponseIsSuccessful();

        // Vérifie route correcte
        $this->assertRouteSame(
            'playlists.showone',
            ['id' => 13]
        );

        // Vérifie contenu page détail
        $this->assertSelectorTextContains(
            'h4',
            self::TITRE_ATTENDU_CSHARP
        );
    }

    /**
     * Test générique des tris playlists
     *
     * @dataProvider providerTriPlaylists
     */
    public function testTriPlaylists(
        string $url,
        string $titreAttendu
    ): void
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            self::SELECTEUR_TITRE_PREMIERE_LIGNE,
            $titreAttendu
        );
    }

    /**
     * Fournit les cas de test des tris playlists
     */
    public function providerTriPlaylists(): array
    {
        return [

            "tri nom ASC" => [
                '/playlists/tri/name/asc',
                self::TITRE_ATTENDU_CSHARP
            ],

            "tri nom DESC" => [
                '/playlists/tri/name/desc',
                'Visual Studio 2019 et C#'
            ],

            "tri nb formations ASC" => [
                '/playlists/tri/nbFormations/asc',
                'Cours de programmation objet'
            ],

            "tri nb formations DESC" => [
                '/playlists/tri/nbFormations/desc',
                self::TITRE_ATTENDU_CSHARP
            ],

        ];
    }


    /**
     * Vérifie que le filtre texte fonctionne
     *
     * @dataProvider providerFiltresTexte
     */
    public function testFiltresTexte(
        string $valeurRecherche,
        int $nbResultatsAttendus,
        string $titreAttendu
    ): void
    {
        $client = static::createClient();
        $client->request('GET', self::RACINE);
        $crawler = $client->submitForm(
            'filtrer',
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

            "filtre nom C#" => [
                'C#',
                2,
                self::TITRE_ATTENDU_CSHARP
            ],
        ];
    }


    /**
     * Vérifie que le filtre par catégorie fonctionne correctement
     */
    public function testFiltreParCategorie(): void
    {
        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            self::RACINE
        );

        $this->assertResponseIsSuccessful();

        // Sélection formulaire catégories
        $form = $crawler
            ->filter('form[action*="categories"]')
            ->form();

        // Simulation sélection catégorie id = 3
        $form['recherche'] = '3';

        $crawler = $client->submit($form);

        // Vérifie nombre résultats
        $this->assertCount(
            2,
            $crawler->filter(
                self::SELECTEUR_TITRES_TABLEAU
            )
        );

        // Vérifie premier résultat
        $this->assertSelectorTextContains(
            self::SELECTEUR_TITRE_PREMIERE_LIGNE,
            self::TITRE_ATTENDU_CSHARP
        );
    }
}

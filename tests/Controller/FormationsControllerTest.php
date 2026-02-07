<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests fonctionnels du contrôleur FormationsController
 */
class FormationsControllerTest extends WebTestCase
{
    private const RACINE = '/formations';
    private const SELECTEUR_TITRE_PREMIERE_LIGNE = 'tbody tr:first-child h5';
    private const SELECTEUR_TITRES_TABLEAU = 'tbody tr h5';

    /**
     * Vérifie que la page des formations est accessible
     */
    public function testAccesPageFormations(): void
    {
        $client = static::createClient();
        $client->request('GET', self::RACINE);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
    * Vérifie que le clic sur la miniature de la première formation
    * permet d'accéder à la page détail correspondante.
    */
    public function testClicAccessFormation(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::RACINE);
        $this->assertResponseIsSuccessful();
        
        // Récupération du lien de la miniature (colonne image)
        $lien = $crawler
            ->filter('tbody tr:first-child td:last-child a')
            ->link();
        // Simulation du clic utilisateur
        $client->click($lien);
        // Vérifie que la page détail est accessible
        $this->assertResponseIsSuccessful();
        
        // Vérifie que la route générée correspond bien à 'formations.showone' avec l'ID 1
        $this->assertRouteSame('formations.showone', ['id' => 1]);
        
        // Vérifie que le titre attendu est affiché
        $this->assertSelectorTextContains(
            'h4',
            'Eclipse n°8 : Déploiement'
        );
    }

    /**
     * Test générique des tris sur la liste des formations
     *
     * @dataProvider providerTriFormations
     */
    public function testTriFormations(string $url, string $titreAttendu): void
    {
        $client = static::createClient();

        // Accès à la page triée
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();

        // Vérifie la première ligne du tableau
        $this->assertSelectorTextContains(
            self::SELECTEUR_TITRE_PREMIERE_LIGNE,
            $titreAttendu
        );
    }


    /**
     * Utilisation d'un data provider pour DRY et lisibilité :
     * Fournit les cas de tests pour les tris
     */
    public function providerTriFormations(): array
    {
        return [

            "tri titre ASC" => [
                '/formations/tri/title/asc',
                'Android Studio (complément n°1) : Navigation Drawer et Fragment'
            ],

            "tri titre DESC" => [
                '/formations/tri/title/desc',
                'UML : Diagramme de paquetages'
            ],

            "tri playlist ASC" => [
                '/formations/tri/name/asc/playlist',
                'Bases de la programmation n°74 - POO : collections'
            ],

            "tri playlist DESC" => [
                '/formations/tri/name/desc/playlist',
                'C# : ListBox en couleur'
            ],

            "tri date ASC" => [
                '/formations/tri/publishedAt/asc',
                "Cours UML (1 à 7 / 33) : introduction et cas d'utilisation"
            ],

            "tri date DESC" => [
                '/formations/tri/publishedAt/desc',
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
        string $premierTitreAttendu
    ): void
    {
        $client = static::createClient();
        $client->request('GET', self::RACINE);

        $crawler = $client->submitForm('filtrer', [
            'recherche' => $valeurRecherche
        ]);

        $this->assertCount(
            $nbResultatsAttendus,
            $crawler->filter(self::SELECTEUR_TITRES_TABLEAU)
        );

        $this->assertSelectorTextContains(
            self::SELECTEUR_TITRE_PREMIERE_LIGNE,
            $premierTitreAttendu
        );
    }
    
    /**
    * Fournit les cas de tests des filtres texte
    */
    public function providerFiltresTexte(): array
    {
        return [

            "filtre par nom exact" => [
                'UML : Diagramme de paquetages',
                1,
                'UML : Diagramme de paquetages'
            ],

            "filtre par playlist" => [
                'uml',
                10,
                'UML : Diagramme de paquetages'
            ],

        ];
    }
    
    /**
    * Vérifie que le filtre par catégorie fonctionne correctement.
    */
   public function testFiltreParCategorie(): void
   {
       $client = static::createClient();

       // Accès à la page liste formations
       $crawler = $client->request('GET', self::RACINE);

       $this->assertResponseIsSuccessful();

       // Sélection du formulaire "catégories"
       $form = $crawler
        ->filter('form[action*="categories"]')
        ->form();

       // Simulation du choix de la catégorie id = 2
       $form['recherche'] = '2';

       // Soumission du formulaire
       $crawler = $client->submit($form);

       // Vérifie le nombre de résultats affichés
       $this->assertCount(
           11,
           $crawler->filter(self::SELECTEUR_TITRES_TABLEAU)
       );

       // Vérifie le titre de la première formation affichée
       $this->assertSelectorTextContains(
           self::SELECTEUR_TITRE_PREMIERE_LIGNE,
           'Eclipse n°2 : rétroconception avec ObjectAid'
       );
   }
}

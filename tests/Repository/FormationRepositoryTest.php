<?php

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Tests unitaires du repository Formation.
 *
 * Vérifie les opérations principales :
 * - ajout d'une formation
 * - suppression d'une formation
 * - tri personnalisé
 * - recherche par valeur contenue
 * - récupération des dernières formations
 * - récupération des formations par playlist
 */
class FormationRepositoryTest extends KernelTestCase
{
    /**
     * Gestionnaire d'entités Doctrine utilisé pour les tests.
     *
     * @var EntityManagerInterface|null
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Repository de l'entité Formation.
     *
     * @var mixed
     */
    private $repository;

    /**
     * Initialise le kernel Symfony et prépare le repository pour les tests.
     *
     * Symfony exécute automatiquement cette méthode avant chaque test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = static::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager
            ->getRepository(Formation::class);
        
        // démarre une transaction
        $this->entityManager->beginTransaction();
    }

    /**
     * Crée une nouvelle instance de formation pour les tests.
     *
     * @param string $title Titre de la formation
     * @return Formation
     */
    private function newFormation(string $title): Formation
    {
        $playlist = new Playlist();
        $this->entityManager->persist($playlist);

        return (new Formation())
            ->setTitle($title)
            ->setVideoId("test123")
            ->setPlaylist($playlist)
            ->setPublishedAt(new \DateTime());
    }

    /**
     * Teste l'ajout d'une formation dans le repository.
     *
     * @return void
     */
    public function testAddFormation(): void
    {
        $formation = $this->newFormation("Formation test add");
        $nbFormations = $this->repository->count([]);
        $this->repository->add($formation);

        $this->assertEquals(
            $nbFormations + 1,
            $this->repository->count([]),
            "Erreur lors de l'ajout"
        );
    }

    /**
     * Teste la suppression d'une formation dans le repository.
     *
     * @return void
     */
    public function testRemoveFormation(): void
    {
        $formation = $this->newFormation("Formation test remove");
        $this->repository->add($formation);
        $nbFormations = $this->repository->count([]);
        $this->repository->remove($formation);

        $this->assertEquals(
            $nbFormations - 1,
            $this->repository->count([]),
            "Erreur lors de la suppression"
        );
    }

    /**
     * Teste la récupération de toutes les formations triées selon un champ donné.
     *
     * @return void
     */
    public function testFindAllOrderBy(): void
    {
        $formationA = $this->newFormation("AAA");
        $formationB = $this->newFormation("BBB");

        $this->repository->add($formationA);
        $this->repository->add($formationB);

        $formations = $this->repository->findAllOrderBy("title", "ASC");
        
        // on transforme la liste de formations en liste des titres des formations
        $titres = [];
        foreach ($formations as $formation) {
            $titres[] = $formation->getTitle();
        }
        
        $indexA = array_search("AAA", $titres);
        $indexB = array_search("BBB", $titres);

        $this->assertNotFalse($indexA);
        $this->assertNotFalse($indexB);

        $this->assertTrue(
            $indexA < $indexB,
            "AAA doit apparaître avant BBB"
        );
    }

    /**
     * Teste la recherche d'une formation par valeur contenue dans un champ donné.
     *
     * @return void
     */
    public function testFindByContainValue(): void
    {
        // Génère une chaine aléatoire en hexadécimal pur
        $random = bin2hex(random_bytes(4));
        $formation = $this->newFormation("Test $random Symfony");
        $this->repository->add($formation);

        $results = $this->repository->findByContainValue("title", $random);

        $this->assertCount(1, $results, "Le résultat devrait être 1");
        $this->assertSame($formation->getId(), $results[0]->getId());
    }

    /**
     * Teste la récupération des dernières formations publiées.
     *
     * @return void
     */
    public function testFindAllLasted(): void
    {
        // On crée une formation avec une date légèrement plus récente que "now"
        // Cela respecte la contrainte <= today
        $formation = $this->newFormation("Test Lasted");
        $formation->setPublishedAt(new \DateTime('now +1 second'));
        $this->repository->add($formation);

        // On récupère le nombre total d'éléments
        $all = $this->repository->findAll();
        $trueCount = count($all);

        // On teste avec min(3, nombre total)
        $testCount = $trueCount < 3 ? $trueCount : 3;

        $results = $this->repository->findAllLasted($testCount);

        $this->assertCount($testCount, $results);

        // On vérifie que la formation ajoutée est bien la plus récente
        $this->assertSame(
            $formation->getTitle(),
            $results[0]->getTitle(), // 0 car tri DESC
            "La formation ajoutée devrait être la plus récente"
        );
    }

    /**
     * Teste la récupération des formations associées à une playlist donnée.
     *
     * @return void
     */
    public function testFindAllForOnePlaylist(): void
    {
        $playlist = new Playlist();
        $playlist->setName("Test playlist");
        $this->entityManager->persist($playlist);

        // Formations dans cette playlist (avec dates croissantes)
        $formationA = $this->newFormation("AAA")
            ->setPublishedAt(new \DateTime("2020-01-01"))
            ->setPlaylist($playlist);
        $this->repository->add($formationA);

        $formationB = $this->newFormation("BBB")
            ->setPublishedAt(new \DateTime("2020-01-02"))
            ->setPlaylist($playlist);
        $this->repository->add($formationB);

        // Formation dans une autre playlist (ne doit PAS apparaître)
        $autrePlaylist = new Playlist();
        $autrePlaylist->setName("Autre playlist");
        $this->entityManager->persist($autrePlaylist);

        $formationC = $this->newFormation("CCC")
            ->setPublishedAt(new \DateTime("2020-01-03"))
            ->setPlaylist($autrePlaylist);
        $this->repository->add($formationC);

        $this->entityManager->flush();

        $results = $this->repository->findAllForOnePlaylist($playlist->getId());

        // Vérifications
        $this->assertCount(2, $results, "Seules les formations de la playlist doivent être retournées");

        // Vérifie l'ordre ASC
        $this->assertSame("AAA", $results[0]->getTitle());
        $this->assertSame("BBB", $results[1]->getTitle());
    }
    
    /**
     * Nettoie l'environnement après chaque test en annulant la transaction Doctrine.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // rollback transaction
        if ($this->entityManager !== null &&
            $this->entityManager->getConnection()->isTransactionActive()) {

            $this->entityManager->rollback();
        }

        if ($this->entityManager !== null) {
            $this->entityManager->close();
        }

        $this->entityManager = null;
    }
}

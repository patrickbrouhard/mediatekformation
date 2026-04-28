<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Tests unitaires du repository Categorie.
 *
 * Vérifie les opérations principales :
 * - ajout d'une catégorie
 * - suppression d'une catégorie
 * - récupération des catégories associées à une playlist
 */
class CategorieRepositoryTest extends KernelTestCase
{
    /**
     * Gestionnaire d'entités Doctrine utilisé pour les tests.
     *
     * @var EntityManagerInterface|null
     */
    private ?EntityManagerInterface $entityManager = null;

    /**
     * Repository de l'entité Categorie.
     *
     * @var mixed
     */
    private $repository;

    /**
     * Initialise le kernel Symfony et prépare le repository pour les tests.
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
            ->getRepository(Categorie::class);

        $this->entityManager->beginTransaction();
    }

    /**
     * Crée une nouvelle instance de catégorie pour les tests.
     *
     * @param string $name Nom de la catégorie
     * @return Categorie
     */
    private function newCategorie(string $name): Categorie
    {
        return (new Categorie())->setName($name);
    }

    /**
     * Crée une nouvelle instance de formation associée à une playlist et une catégorie.
     *
     * @param Playlist $playlist Playlist associée
     * @param Categorie $categorie Catégorie associée
     * @return Formation
     */
    private function newFormation(Playlist $playlist, Categorie $categorie): Formation {

        return (new Formation())
            ->setTitle("Formation test")
            ->setVideoId("test123")
            ->setPlaylist($playlist)
            ->addCategory($categorie)
            ->setPublishedAt(new \DateTime());
    }

    /**
     * Teste l'ajout d'une catégorie dans le repository.
     *
     * @return void
     */
    public function testAddCategorie(): void
    {
        $categorie = $this->newCategorie("Categorie test");
        $nbCategories = $this->repository->count([]);
        $this->repository->add($categorie);

        $this->assertEquals(
            $nbCategories + 1,
            $this->repository->count([]),
            "Erreur lors de l'ajout d'une catégorie"
        );
    }

    /**
     * Teste la suppression d'une catégorie dans le repository.
     *
     * @return void
     */
    public function testRemoveCategorie(): void
    {
        $categorie = $this->newCategorie("Categorie test");
        $this->repository->add($categorie);
        $nbCategories = $this->repository->count([]);

        $this->repository->remove($categorie);

        $this->assertEquals(
            $nbCategories - 1,
            $this->repository->count([]),
            "Erreur lors de la suppression d'une catégorie"
        );
    }

    /**
     * Teste la récupération des catégories associées à une playlist donnée.
     *
     * @return void
     */
    public function testFindAllForOnePlaylist(): void
    {
        $playlist = new Playlist();
        $playlist->setName("Playlist test");
        $this->entityManager->persist($playlist);
        $categorie = $this->newCategorie("SymfonyTest");
        $this->repository->add($categorie);

        $formation = $this->newFormation($playlist, $categorie);
        $this->entityManager->persist($formation);
        $this->entityManager->flush();

        $results = $this->repository
            ->findAllForOnePlaylist($playlist->getId());

        $this->assertCount(1, $results);

        $this->assertSame(
            "SymfonyTest",
            $results[0]->getName()
        );
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

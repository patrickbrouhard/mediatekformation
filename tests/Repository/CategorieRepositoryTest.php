<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class CategorieRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private $repository;

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

    private function newCategorie(string $name): Categorie
    {
        return (new Categorie())->setName($name);
    }

    private function newFormation(Playlist $playlist, Categorie $categorie): Formation {

        return (new Formation())
            ->setTitle("Formation test")
            ->setVideoId("test123")
            ->setPlaylist($playlist)
            ->addCategory($categorie)
            ->setPublishedAt(new \DateTime());
    }

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

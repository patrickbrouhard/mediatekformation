<?php

namespace App\Tests\Repository;

use App\Entity\Playlist;
use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class PlaylistRepositoryTest extends KernelTestCase
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
            ->getRepository(Playlist::class);

        // démarre une transaction
        $this->entityManager->beginTransaction();
    }

    private function newPlaylist(string $name): Playlist
    {
        return (new Playlist())->setName($name);
    }

    private function newFormation(Playlist $playlist, string $title): Formation
    {
        return (new Formation())
            ->setTitle($title)
            ->setVideoId("test123")
            ->setPlaylist($playlist)
            ->setPublishedAt(new \DateTime());
    }

    public function testAddPlaylist(): void
    {
        $playlist = $this->newPlaylist("Playlist test add");
        $nbFormations = $this->repository->count([]);
        $this->repository->add($playlist);

        $this->assertEquals(
            $nbFormations + 1,
            $this->repository->count([]),
            "Erreur lors de l'ajout d'une playlist"
        );
    }

    public function testRemovePlaylist(): void
    {
        $playlist = $this->newPlaylist("Playlist test remove");
        $this->repository->add($playlist);
        $nbPlaylists = $this->repository->count([]);

        $this->repository->remove($playlist);

        $this->assertEquals(
            $nbPlaylists - 1,
            $this->repository->count([]),
            "Erreur lors de la suppression d'une playlist"
        );
    }

    public function testFindAllOrderByName(): void
    {
        $playlistA = $this->newPlaylist("AAA");
        $playlistB = $this->newPlaylist("BBB");

        $this->repository->add($playlistA);
        $this->repository->add($playlistB);

        $playlists = $this->repository->findAllOrderByName("ASC");
        
        // on transforme la liste de formations en liste des titres des formations
        $titres = [];
        foreach ($playlists as $playlist) {
            $titres[] = $playlist->getName();
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

    public function testFindAllOrderByNbFormations(): void
    {
        $playlist1 = $this->newPlaylist("Playlist 1");
        $playlist2 = $this->newPlaylist("Playlist 2");

        $this->repository->add($playlist1);
        $this->repository->add($playlist2);

        $formation1 = $this->newFormation($playlist1, "Formation 1");

        $this->entityManager->persist($formation1);
        $this->entityManager->flush();

        $results = $this->repository->findAllOrderByNbFormations("DESC");
        $titres = [];
        foreach ($results as $playlist) {
            $titres[] = $playlist->getName();
        }
        
        $indexA = array_search("Playlist 1", $titres);
        $indexB = array_search("Playlist 2", $titres);

        $this->assertNotFalse($indexA);
        $this->assertNotFalse($indexB);
        
        $this->assertTrue(
            $indexA < $indexB,
            "Playlist 1 doit apparaître avant Playlist 2"
        );
    }

    public function testFindByContainValue(): void
    {
        // Génère une chaine aléatoire en hexadécimal pur
        $random = bin2hex(random_bytes(4));
        $playlist = $this->newPlaylist("Playlist $random");
        $this->repository->add($playlist);

        $results = $this->repository->findByContainValue("name", $random);

        $this->assertCount(1, $results, "Le résultat devrait être 1");
        $this->assertSame(
            $playlist->getName(),
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

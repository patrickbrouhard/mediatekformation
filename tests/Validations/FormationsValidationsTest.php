<?php

namespace App\Tests\Entity;

use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Tests de validation pour l'entité Formation.
 */
class FormationValidationTest extends KernelTestCase
{
    /**
     * Validateur Symfony utilisé pour tester les contraintes.
     *
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * Initialise le kernel et récupère le validator avant chaque test.
     * Symfony utilise setUp automatiquement (si présent) avant chaque test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }
    
    /**
     * Crée une entité Formation valide par défaut.
     * Il n'est pas nécessaire de créer une "vraie" playlist.
     *
     * @return Formation Instance de Formation valide
     */
    private function getFormation(): Formation
    {
        return (new Formation())
            ->setTitle('Test')
            ->setVideoId('id123')
            ->setPlaylist(new Playlist());
    }

    /**
     * Vérifie qu'une date de publication future est invalide.
     *
     * @return void
     */
    public function testPublishedAtCannotBeFuture(): void
    {
        $dateFuture = new \DateTime('+1 day');
        $formation = $this->getFormation()->setPublishedAt($dateFuture);

        $errors = $this->validator->validate($formation);
        $this->assertGreaterThan(
            0,
            count($errors),
            'Une date future devrait déclencher une erreur de validation.'
        );
    }

    /**
     * Vérifie qu'une date de publication passée est valide.
     *
     * @return void
     */
    public function testPublishedAtCanBePast(): void
    {
        $datePast = new \DateTime('-1 day');
        $formation = $this->getFormation()->setPublishedAt($datePast);

        $errors = $this->validator->validate($formation);
        $this->assertCount(
            0,
            $errors,
            'Une date passée ne devrait générer aucune erreur de validation.'
        );
    }
}

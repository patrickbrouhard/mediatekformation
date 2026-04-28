<?php

namespace App\Tests\Entity;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires de la classe Formation.
 *
 * Vérifie le comportement des méthodes utilitaires liées aux dates de publication.
 */
class FormationTest extends TestCase
{
    /**
     * Attendu : quand publishedAt est null -> la méthode retourne ""
     * @return void
     */
    public function testGetPublishedAtStringWithNullDate(): void
    {
        $formation = new Formation();

        $this->assertEquals("", $formation->getPublishedAtString());
    }

    /**
     * Attendu : quand publishedAt contient une date -> la méthode retourne la date au format d/m/Y
     * @return void
     */
    public function testGetPublishedAtStringWithDate(): void
    {
        $formation = new Formation();

        $date = new \DateTime("2025-11-16");
        $formation->setPublishedAt($date);

        $this->assertEquals("16/11/2025", $formation->getPublishedAtString());
    }
}

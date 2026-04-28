<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Représente une catégorie de formation.
 */
#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Cette catégorie existe déjà.')]
class Categorie
{
    /**
     * Identifiant unique de la catégorie.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de la catégorie.
     */
    #[ORM\Column(length: 50, unique: true)]
    private ?string $name = null;

    /**
     * Liste des formations associées à cette catégorie.
     *
     * @var Collection<int, Formation>
     */
    #[ORM\ManyToMany(targetEntity: Formation::class, mappedBy: 'categories')]
    private Collection $formations;

    /**
     * Constructeur.
     * Initialise la collection des formations.
     */
    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant de la catégorie.
     *
     * @return int|null Identifiant de la catégorie
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le nom de la catégorie.
     *
     * @return string|null Nom de la catégorie
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de la catégorie.
     *
     * @param string|null $name Nom de la catégorie
     * @return static
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Récupère les formations associées à la catégorie.
     *
     * @return Collection<int, Formation> Liste des formations
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    /**
     * Ajoute une formation à la catégorie.
     *
     * @param Formation $formation Formation à ajouter
     * @return static
     */
    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->addCategory($this);
        }

        return $this;
    }

    /**
     * Supprime une formation de la catégorie.
     *
     * @param Formation $formation Formation à supprimer
     * @return static
     */
    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation)) {
            $formation->removeCategory($this);
        }

        return $this;
    }
}

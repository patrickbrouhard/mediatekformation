<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente une playlist de formations.
 */
#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
    /**
     * Identifiant unique de la playlist.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de la playlist.
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    /**
     * Description de la playlist.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * Liste des formations associées à la playlist.
     *
     * @var Collection<int, Formation>
     */
    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'playlist')]
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
     * Récupère l'identifiant de la playlist.
     *
     * @return int|null Identifiant de la playlist
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le nom de la playlist.
     *
     * @return string|null Nom de la playlist
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de la playlist.
     *
     * @param string|null $name Nom de la playlist
     * @return static
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Récupère la description de la playlist.
     *
     * @return string|null Description de la playlist
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description de la playlist.
     *
     * @param string|null $description Description de la playlist
     * @return static
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Récupère les formations associées à la playlist.
     *
     * @return Collection<int, Formation> Liste des formations
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    /**
     * Ajoute une formation à la playlist.
     *
     * @param Formation $formation Formation à ajouter
     * @return static
     */
    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setPlaylist($this);
        }

        return $this;
    }

    /**
     * Supprime une formation de la playlist.
     *
     * @param Formation $formation Formation à supprimer
     * @return static
     */
    public function removeFormation(Formation $formation): static
    {
        $wasRemoved = $this->formations->removeElement($formation);
        
        if ($wasRemoved && $formation->getPlaylist() === $this) {
            // Si la formation est encore attachée à cette playlist,
            // on maj le côté propriétaire en la détachant (playlist = null)
            $formation->setPlaylist(null);
        }
        return $this;
    }
    
    /**
     * Récupère la liste des noms de catégories présentes dans la playlist.
     *
     * @return Collection<int, string> Liste des noms de catégories
     */
    public function getCategoriesPlaylist() : Collection
    {
        $categories = new ArrayCollection();
        foreach ($this->formations as $formation) {
            $categoriesFormation = $formation->getCategories();
            foreach ($categoriesFormation as $categorieFormation) {
                if (!$categories->contains($categorieFormation->getName())) {
                    $categories[] = $categorieFormation->getName();
                }
            }
        }
        return $categories;
    }
}

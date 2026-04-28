<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Représente une formation.
 */
#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{

    /**
     * Début de chemin vers les images.
     */
    private const CHEMIN_IMAGE = "https://i.ytimg.com/vi/";
        
    /**
     * Identifiant unique de la formation.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Date de publication de la formation.
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\LessThanOrEqual(
            "today",
            message: "La date de création doit être antérieure ou égale à aujourd'hui."
    )]
    private ?\DateTimeInterface $publishedAt = null;

    /**
     * Titre de la formation.
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    /**
     * Description de la formation.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * Identifiant de la vidéo associée (YouTube).
     */
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $videoId = null;

    /**
     * Playlist associée à la formation.
     */
    #[ORM\ManyToOne(inversedBy: 'formations')]
    private ?Playlist $playlist = null;

    /**
     * Liste des catégories associées à la formation.
     *
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'formations')]
    private Collection $categories;

    /**
     * Constructeur.
     * Initialise la collection des catégories.
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant de la formation.
     *
     * @return int|null Identifiant de la formation
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère la date de publication.
     *
     * @return \DateTimeInterface|null Date de publication
     */
    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * Définit la date de publication.
     *
     * @param \DateTimeInterface|null $publishedAt Date de publication
     * @return static
     */
    public function setPublishedAt(?\DateTimeInterface $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Récupère la date de publication sous forme de chaîne formatée.
     *
     * @return string Date formatée (d/m/Y) ou chaîne vide si non définie
     */
    public function getPublishedAtString(): string
    {
        if ($this->publishedAt == null) {
            return "";
        }
        return $this->publishedAt->format('d/m/Y');
    }
    
    /**
     * Récupère le titre de la formation.
     *
     * @return string|null Titre de la formation
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Définit le titre de la formation.
     *
     * @param string|null $title Titre de la formation
     * @return static
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Récupère la description de la formation.
     *
     * @return string|null Description de la formation
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description de la formation.
     *
     * @param string|null $description Description de la formation
     * @return static
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Récupère l'identifiant de la vidéo.
     *
     * @return string|null Identifiant de la vidéo
     */
    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    /**
     * Définit l'identifiant de la vidéo.
     *
     * @param string|null $videoId Identifiant de la vidéo
     * @return static
     */
    public function setVideoId(?string $videoId): static
    {
        $this->videoId = $videoId;

        return $this;
    }

    /**
     * Récupère l'URL de la miniature de la vidéo.
     *
     * @return string|null URL de la miniature
     */
    public function getMiniature(): ?string
    {
        return self::CHEMIN_IMAGE.$this->videoId."/default.jpg";
    }

    /**
     * Récupère l'URL de l'image haute qualité de la vidéo.
     *
     * @return string|null URL de l'image
     */
    public function getPicture(): ?string
    {
        return self::CHEMIN_IMAGE.$this->videoId."/hqdefault.jpg";
    }
    
    /**
     * Récupère la playlist associée.
     *
     * @return Playlist|null Playlist associée
     */
    public function getPlaylist(): ?playlist
    {
        return $this->playlist;
    }

    /**
     * Définit la playlist associée.
     *
     * @param Playlist|null $playlist Playlist associée
     * @return static
     */
    public function setPlaylist(?Playlist $playlist): static
    {
        $this->playlist = $playlist;

        return $this;
    }

    /**
     * Récupère les catégories associées à la formation.
     *
     * @return Collection<int, Categorie> Liste des catégories
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * Ajoute une catégorie à la formation.
     *
     * @param Categorie $category Catégorie à ajouter
     * @return static
     */
    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /**
     * Supprime une catégorie de la formation.
     *
     * @param Categorie $category Catégorie à supprimer
     * @return static
     */
    public function removeCategory(Categorie $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }
}

<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Playlist.
 *
 * @extends ServiceEntityRepository<Playlist>
 */
class PlaylistRepository extends ServiceEntityRepository
{
    /**
     * Constructeur.
     *
     * @param ManagerRegistry $registry Registre des gestionnaires Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    /**
     * Ajoute une playlist en base de données.
     *
     * @param Playlist $entity Entité à persister
     * @return void
     */
    public function add(Playlist $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Supprime une playlist de la base de données.
     *
     * @param Playlist $entity Entité à supprimer
     * @return void
     */
    public function remove(Playlist $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne toutes les playlists triées par nom.
     *
     * @param string $ordre Sens du tri (ASC ou DESC)
     * @return Playlist[] Liste des playlists
     */
    public function findAllOrderByName($ordre): array
    {
        return $this->createQueryBuilder('p')
                        ->leftjoin('p.formations', 'f')
                        ->groupBy('p.id')
                        ->orderBy('p.name', $ordre)
                        ->getQuery()
                        ->getResult();
    }

    /**
     * Retourne toutes les playlists triées selon le nombre de formations.
     * Le COUNT(f.id) est utilisé uniquement pour effectuer le tri et
     * il est HIDDEN pour qu'il ne soit pas présent dans les résultats.
     * On retourne donc uniquement des entités Playlist.
     *
     * @param string $ordre Sens du tri (ASC ou DESC)
     * @return Playlist[] Liste des playlists
     */
    public function findAllOrderByNbFormations($ordre): array
    {
        return $this->createQueryBuilder('p')
        // Jointure de p vers f afin que tous les p apparaissent, même si pas de f
        ->leftJoin('p.formations', 'f')
        // Ajoute le COUNT(f.id) uniquement pour le tri
        // HIDDEN car on en a pas besoin pour afficher le nombre
        ->addSelect('COUNT(f.id) AS HIDDEN nbFormations')
        ->groupBy('p.id')
        ->orderBy('nbFormations', $ordre)
        ->addOrderBy('p.name', 'ASC')
        ->getQuery()
        ->getResult();
    }

    /**
     * Retourne les playlists dont un champ contient une valeur donnée,
     * ou toutes les playlists si la valeur est vide.
     *
     * @param string $champ Nom du champ
     * @param string $valeur Valeur recherchée
     * @param string $table Nom de la relation si le champ appartient à une autre table
     * @return Playlist[] Liste des playlists
     */
    public function findByContainValue($champ, $valeur, $table = ""): array
    {
        if ($valeur == "") {
            return $this->findAllOrderByName('ASC');
        }
        if ($table == "") {
            return $this->createQueryBuilder('p')
                            ->leftjoin('p.formations', 'f')
                            ->where('p.' . $champ . ' LIKE :valeur')
                            ->setParameter('valeur', '%' . $valeur . '%')
                            ->groupBy('p.id')
                            ->orderBy('p.name', 'ASC')
                            ->getQuery()
                            ->getResult();
        } else {
            return $this->createQueryBuilder('p')
                            ->leftjoin('p.formations', 'f')
                            ->leftjoin('f.categories', 'c')
                            ->where('c.' . $champ . ' LIKE :valeur')
                            ->setParameter('valeur', '%' . $valeur . '%')
                            ->groupBy('p.id')
                            ->orderBy('p.name', 'ASC')
                            ->getQuery()
                            ->getResult();
        }
    }
}

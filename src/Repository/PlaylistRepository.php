<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    public function add(Playlist $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(Playlist $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne toutes les playlists triées sur le nom de la playlist
     * @param type $champ
     * @param type $ordre
     * @return Playlist[]
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
    * il est HIDDEN pour qu'il ne soit pas présent dans les résultats
    * On retourne donc uniquement des entités Playlist, sans rien de polluant
    *
    * @param string $ordre : sens du tri (ASC ou DESC)
    * @return array
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
        ->getQuery()
        ->getResult();
    }

    /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @param type $table si $champ dans une autre table
     * @return Playlist[]
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

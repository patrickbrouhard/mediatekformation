# Dossier `Repository`

Ce dossier centralise l’accès aux données via Doctrine. Les controllers y délèguent les requêtes (tri, filtre, agrégations).

## Rôle

Chaque repository est responsable de l'accès aux données d'une entité donnée.
Il centralise les requêtes Doctrine afin d'éviter de disperser la logique d'accès aux données dans les contrôleurs.

Exemple :
```php
$formations = $formationRepository->findAllLasted(5);
```

## Conventions
- Utiliser le `QueryBuilder` pour les requêtes métiers.
- Toujours binder les valeurs utilisateur via paramètres (`:valeur`) pour éviter les injections SQL.

## Aperçu des repositories
- `FormationRepository`, associé à l’entité `Formation`.
    - Fournit des méthodes utilitaires pour l'ajout et la suppression d'une formation.
    - Permet :
      - le tri dynamique (`findAllOrderBy()`) ;
      - la recherche par correspondance partielle (`findByContainValue()`) ;
      - la récupération des dernières formations publiées (`findAllLasted()`) ;
      - la récupération des formations d'une playlist (`findAllForOnePlaylist()`).
  
- `PlaylistRepository`, associé à l’entité `Playlist`.
    - Fournit des méthodes utilitaires pour l'ajout et la suppression d'une playlist.
    - Tri par nom (`findAllOrderByName()`), tri par nombre de formations (`findAllOrderByNbFormations()`),
      rechercher des playlists par correspondance partielle sur leurs propriétés ou sur les catégories associées (`findByContainValue()`).
    - Utilise des agrégations (`COUNT`) et des jointures Doctrine pour les requêtes personnalisées.

- `CategorieRepository`, associé à l’entité `Categorie`.
    - Fournit des méthodes utilitaires pour l'ajout et la suppression d'une catégorie.
    - Permet de récupérer les catégories associées aux formations d'une playlist donnée (`findAllForOnePlaylist()`).
    - Utilise des jointures Doctrine pour les requêtes personnalisées.

- `UserRepository`, associé à l’entité `User`.
    - Intégré au système de sécurité de Symfony.
    - Upgrade automatique du mot de passe (rehash).

## Points d’attention
- Centraliser la logique d'accès aux données dans les repositories plutôt que dans les contrôleurs.
- Certaines méthodes permettent un tri ou une recherche dynamiques en fonction du champ demandé.
- Certaines requêtes s'appuient sur des jointures et des agrégations (`COUNT`) pour effectuer des tris ou des filtres avancés.

## Remarque sur les paramètres de requête

Les valeurs provenant de l'utilisateur devraient être transmises aux requêtes Doctrine via des paramètres nommés.

Exemple :

```php
return $this->createQueryBuilder('f')
    ->where('f.title LIKE :valeur')
    ->setParameter('valeur', '%'.$valeur.'%')
    ->getQuery()
    ->getResult();
```

L'utilisation de `setParameter()` permet à Doctrine de gérer correctement l'échappement des valeurs et d'éviter les injections SQL.

À éviter :

```php
->where("f.title LIKE '%".$valeur."%'")
```

qui construit directement la requête à partir d'une valeur externe.

## Exemples d’utilisation
```php
$formations = $formationRepository->findAllOrderBy('publishedAt', 'DESC');
$playlists = $playlistRepository->findAllOrderByNbFormations('ASC');
```

## Liens internes
- [`Entity/`](../Entity)
- [`Controller/`](../Controller)

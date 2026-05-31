# Dossier `Entity`

Ce dossier contient le modèle métier Doctrine (entités, relations, contraintes). Il décrit la structure de la base de données et les règles métier de base.

## Conventions
- Mapping Doctrine par attributs (`#[ORM\...]`).
- Relations synchronisées via méthodes `add/remove`.
- Les contraintes métier simples peuvent être ajoutées via Symfony Validator.

## Les entités
- `Formation` : vidéo de formation (titre, date, description, videoId).
    - Relation : `ManyToOne` vers `Playlist`, `ManyToMany` vers `Categorie`.
    - Validation : Validation de la date de publication (doit être antérieure ou égale à aujourd'hui).
    - Fournit des méthodes utilitaires pour :
      - formater la date (`getPublishedAtString()`) ;
      - générer l'URL de la miniature YouTube (`getMiniature()`) ;
      - générer l'URL de l'image haute qualité YouTube (`getPicture()`).

- `Playlist` : regroupe des formations.
    - Stock le nom et la description d'une playlist.
    - Relation : `OneToMany` vers `Formation`.
    - Assure la cohérence de la [relation bidirectionnelle](#relations-bidirectionnelles) lors de l'ajout ou de la suppression d'une formation.
    - Fournit une méthode utilitaire `getCategoriesPlaylist()` pour récupérer la liste unique des catégories présentes dans les formations de la playlist.

- `Categorie` : Les catégories des formations.
    - Stock le nom d’une catégorie.
    - Unicité du nom (DB + validation).
    - Relation : `ManyToMany` avec `Formation`.
    - Assure la cohérence de la [relation bidirectionnelle](#relations-bidirectionnelles) lors de l'ajout ou de la suppression d'une formation.

- `User` : utilisateur pour l’authentification Symfony.
    - Utilisée par le système d'authentification de Symfony.
    - stock rôles, mot de passe hashé, identifiant `username`.
    - Unicité sur le nom d'utilisateur.

## Points d’attention
- Respecter l’unicité des catégories.
- La date de publication ne doit pas être future.
- Garder la cohérence des [relations bidirectionnelles](#relations-bidirectionnelles).

## Exemples d’usage
```php
$formation->setPlaylist($playlist);
$formation->addCategory($categorie);
```

## Relations bidirectionnelles

Dans une relation bidirectionnelle Doctrine, comme `Playlist` ↔ `Formation`, **Doctrine ne synchronise pas automatiquement les deux côtés de la relation**. Le côté `ManyToOne` (ici `Formation`) est le *côté propriétaire* : c’est lui qui détermine la valeur réellement enregistrée en base.  
Pour garantir la cohérence entre les deux objets, il est donc nécessaire de mettre à jour **les deux côtés** lors de l’ajout ou de la suppression d’une formation dans une playlist.

Par exemple, dans la méthode `addFormation()` de l’entité `Playlist`, on ajoute la formation à la collection **et** on met à jour le côté propriétaire :

```php
public function addFormation(Formation $formation): static
{
    if (!$this->formations->contains($formation)) {
        $this->formations->add($formation);
        $formation->setPlaylist($this); // synchronisation du côté propriétaire
    }

    return $this;
}
```

De même, lors de la suppression, on retire la formation de la collection **et** on détache la playlist du côté propriétaire :

```php
public function removeFormation(Formation $formation): static
{
    $wasRemoved = $this->formations->removeElement($formation);

    if ($wasRemoved && $formation->getPlaylist() === $this) {
        $formation->setPlaylist(null); // suppression cohérente de la relation
    }

    return $this;
}
```

Cette synchronisation manuelle est indispensable pour éviter des incohérences entre les objets PHP et les données réellement persistées en base.


## Liens internes
- [`Repository/`](../Repository)
- [`Form/`](../Form)

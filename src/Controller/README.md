# `Controller`

Ce dossier contient les contrôleurs Symfony qui exposent les routes HTTP, orchestrent les appels aux repositories et rendent les vues Twig.

## Conventions
- Un contrôleur = un périmètre de domaine clair (accueil, formations, playlists, auth).
- suffixe `Controller` pour les classes.
- Les routes sont définies via attributs `#[Route]`.
- Les contrôleurs restent légers : pas de logique SQL ou autres ici, on délègue aux repositories.

## Aperçu des contrôleurs
- `AccueilController` : page d’accueil et CGU.
    - Routes : `/` (accueil), `/cgu` (conditions).
- `FormationsController` : liste, tri, recherche et détail d’une formation.
    - Exemples : `/formations`, `/formations/tri/{champ}/{ordre}/{table}`, `/formations/formation/{id}`.
- `PlaylistsController` : liste, tri, recherche et détail d’une playlist.
    - Exemples : `/playlists`, `/playlists/tri/{champ}/{ordre}`, `/playlists/playlist/{id}`.
- `LoginController` : login/logout et redirection admin.
    - Exemples : `/login`, `/logout`, `/admin`.
- `admin/` : back-office (CRUD).

## Points d’attention
- Vérifier les IDs inexistants et renvoyer une erreur 404 si besoin.
- Valider/normaliser les paramètres de tri et de recherche.
- Le back-office est protégé par la sécurité Symfony (ROLE_ADMIN).

## Exemples d’utilisation
```php
return $this->render('pages/formations.html.twig', [
    'formations' => $formations,
    'categories' => $categories,
]);
```

## Liens internes
- [Back-office `admin/`](./admin)
- [`Repository/`](../Repository)
- [`Form/`](../Form)
- [`Entity/`](../Entity)
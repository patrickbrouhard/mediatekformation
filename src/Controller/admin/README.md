# Dossier `Controller/admin`

Ce dossier regroupe les contrôleurs de back-office pour gérer les formations, playlists et catégories. Toutes ces routes sont protégées par `ROLE_ADMIN`.

## Conventions
- Toutes les routes commencent par `/admin`.
- Ajout/édition via `FormType` dédiés.
- Les suppressions vérifient les dépendances (ex : pas de suppression si lié).

## Aperçu des contrôleurs
- `AdminFormationsController`
    - Liste, tri, recherche : `/admin/formations`
    - Ajout : `/admin/formation/ajout`
    - Édition : `/admin/formation/edit/{id}`
    - Suppression : `/admin/formation/suppr/{id}`
- `AdminPlaylistsController`
    - Liste, tri, recherche : `/admin/playlists`
    - Ajout : `/admin/playlist/ajout`
    - Édition : `/admin/playlist/edit/{id}`
    - Suppression : `/admin/playlist/suppr/{id}` (bloquée si formations liées)
- `AdminCategoriesController`
    - Liste/ajout : `/admin/categories`
    - Suppression : `/admin/categorie/suppr/{id}` (bloquée si formations liées)

## Points d’attention
- Vérifier l’existence d’une entité avant modification/suppression.
- Utiliser les flash messages pour le feedback utilisateur.
- Garder cohérence entre règles métier et validations (Form/Entity).

## Liens internes
- [`Form/`](../../Form)
- [`Repository/`](../../Repository)
- [`Entity/`](../../Entity)

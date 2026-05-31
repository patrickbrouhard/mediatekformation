# Dossier `Form`

Ce dossier contient les formulaires Symfony utilisés pour la création et la modification des entités depuis le back-office.

## Conventions
- `data_class` indique à Symfony dans quelle entité injecter automatiquement les données du formulaire (Ex : `FormationType` → `Formation`).
- Validation côté formulaire (NotBlank, Length, LessThanOrEqual, etc.).
- Les relations passent par `EntityType`.

## Aperçu des formulaires
- `FormationType` : création/édition d’une formation, associé à l'entité `Formation`.
    - Gère les champs : titre, vidéo YouTube, description, date, playlist et catégories.
    - Validation : titre (NotBlank), vidéo YouTube (NotBlank), date (≤ aujourd’hui), playlist (obligatoire).
    - Permet la sélection :
      - d'une playlist (`EntityType`) ;
      - de plusieurs catégories (`EntityType` multiple).
    - Initialise automatiquement la date du jour lors de la création d'une nouvelle formation.

- `PlaylistType` : création/édition d’une playlist, associé à l'entité `Playlist`.
    - Gère les champs : nom et description.
    - Validation : nom (NotBlank).
    - Fournit un bouton de soumission pour l'enregistrement des données.

- `CategorieType` : création d’une catégorie, associé à l'entité `Categorie`.
    - Gère le champ : nom.
    - Validation : nom (NotBlank, max 50).
    - Fournit un bouton de soumission pour l'enregistrement des données.

## Points d’attention
- Garder cohérence entre contraintes Form et contraintes Entity.
- `playlist` est obligatoire pour une formation.
- La date de publication doit être ≤ aujourd’hui.

## Exemple d’utilisation
```php
$form = $this->createForm(FormationType::class, $formation);
$form->handleRequest($request);
```

## Fonctionnement général

Les formulaires Symfony servent d'intermédiaire entre les données envoyées par l'utilisateur et les entités Doctrine.

Cycle classique :

```php
$form = $this->createForm(FormationType::class, $formation);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    $entityManager->persist($formation);
    $entityManager->flush();
}
```

1. Le contrôleur crée un formulaire associé à une entité.
2. Symfony affiche automatiquement les champs définis dans le `FormType`.
3. Lors de la soumission, les données sont injectées dans l'entité.
4. Les contraintes de validation sont vérifiées.
5. Si le formulaire est valide, l'entité peut être persistée en base.

## Gestion des relations Doctrine

Les relations entre entités sont représentées par des champs `EntityType`.

Exemples :

```php
->add('playlist', EntityType::class)
```

permet de sélectionner une instance de `Playlist`.

```php
->add('categories', EntityType::class, [
    'multiple' => true
])
```

permet de sélectionner plusieurs instances de `Categorie`.

Symfony convertit automatiquement les choix sélectionnés en objets Doctrine.


## Liens internes
- [`Entity/`](../Entity)
- [`Controller/admin/`](../Controller/admin)

# Dossier `DataFixtures`

Ce dossier contient les fixtures Doctrine utilisées pour initialiser des données de test/développement.

## Conventions
- Utiliser `DoctrineFixturesBundle`.
- Hacher les mots de passe avec `UserPasswordHasherInterface`.
- Ne pas réutiliser des identifiants faibles en production (important dans le monde réel).

## Aperçu des fixtures
- `AppFixtures` : fixture “squelette” (flush uniquement).
- `UserFixture` : crée un utilisateur admin par défaut.

## Points d’attention
- Le compte admin de fixture utilise un mot de passe trivial : à remplacer en environnement réel.
- Ne pas supprimer `AppFixtures` même si elle est vide, c'est nécessaire pour Doctrine (à vérifier)

## Exemple d’utilisation
```bash
php bin/console doctrine:fixtures:load
```

## Liens internes
- [`Entity/`](../Entity)
- [`Repository/`](../Repository)
# Dossier `src`

Ce dossier contient tout le code applicatif Symfony (hors tests). Il regroupe les contrôleurs HTTP, le modèle métier Doctrine, les formulaires et les repositories.

## Structure rapide
- `Controller/` : points d’entrée HTTP (routes) + rendu Twig.
- `Controller/admin/` : back-office (CRUD) réservé aux admins.
- `Entity/` : entités Doctrine (modèle métier + relations).
- `Form/` : formulaires Symfony + règles de validation.
- `Repository/` : requêtes Doctrine (tri, filtres, agrégations).
- `DataFixtures/` : données de test/initialisation.
- `Kernel.php` : noyau Symfony de l’application.

## Conventions générales
- Injection de dépendances via constructeur.
- Routage par attributs PHP (`#[Route(...)]`).
- Doctrine via attributs (`#[ORM\...]`) dans les entités.
- Accès DB centralisé dans les repositories.

## Dépendances clés
- **Symfony Framework** (controllers, routing, services).
- **Doctrine ORM/DBAL** (entités, repositories).
- **Symfony Form + Validator** (FormTypes et contraintes).
- **Symfony Security** (authentification + accès `/admin`).
- **Doctrine Fixtures** (DataFixtures).

## Points d’attention
- Toutes les routes `/admin` sont protégées par `ROLE_ADMIN`.
- Les tris/recherches dynamiques reposent sur des champs passés en paramètre : contrôler la liste autorisée côté contrôleur.
- Conserver la cohérence entre contraintes de formulaire et contraintes d’entité.

## Rappels rapides

### Controller
Un contrôleur reçoit une requête HTTP (ex : ouverture d’une page) interagit avec le modèle (via les repositories), execute la logique nécessaire, et retourne une réponse (souvent une page HTML).

### Entity
Une entité représente une table de la base de données. 
Elle contient des propriétés (colonnes) et des relations (ex : ManyToOne). Les entités sont gérées par Doctrine.

### Form
Un FormType définit la structure d’un formulaire (champs, types, contraintes). Il est utilisé pour générer des formulaires HTML et valider les données soumises.
Symfony génère ensuite automatiquement le HTML du formulaire et gère :
- la récupération des données
- la validation
- l’hydratation des entités

### Repository
Un repository est une classe dédiée à l’accès aux données d’une entité.
Il contient des méthodes personnalisées pour récupérer des données selon des critères spécifiques (ex : `findByStatus()`, `findRecent()`, etc.). C’est la couche d’abstraction entre le modèle métier et la base de données.

### DataFixtures
Les fixtures sont des classes qui permettent de charger des données de test ou d'initialisation dans la base de données. 
Elles sont utilisées pour peupler la base avec des données réalistes lors du développement ou des tests.

Utilisé pour :
- avoir des utilisateurs de test
- créer des catégories par défaut
- générer des données de développement

### Résumé

| Répertoire      | Rôle                                           |
| --------------- | ---------------------------------------------- |
| `Controller/`   | Reçoit les requêtes et orchestre l’application |
| `DataFixtures/` | Génère des données de test                     |
| `Entity/`       | Représente les données / tables SQL            |
| `Form/`         | Définit les formulaires Symfony                |
| `Repository/`   | Contient les requêtes vers la base de données  |

## Liens internes utiles
- [`Controller/`](./Controller)
- [`Controller/admin/`](./Controller/admin)
- [`Entity/`](./Entity)
- [`Form/`](./Form)
- [`Repository/`](./Repository)
- [`DataFixtures/`](./DataFixtures)
# Seed SQL – Base de données `mediatekformation`

Le fichier `seed.sql` est un export phpMyAdmin utilisé pour **initialiser la base MySQL** du projet Symfony avec une structure propre et des données prêtes à l’emploi.

Ce fichier permet de reconstruire rapidement une base de données fonctionnelle contenant :
- le schéma complet
- les données de référence
- un catalogue de formations utilisable immédiatement.

Il est principalement destiné aux développeurs et donc, comme son nom l'indique, il sert à créer la BDD de développement.

## Export de la base locale

Export réalisé via phpMyAdmin en mode **Personnalisée**, afin d’obtenir un SQL minimal et propre.

### Options désactivées

- **Sortie**
    - Renommer les bases/tables/colonnes
    - Utiliser `LOCK TABLES`

- **Options de création d’objets**
    - Ajouter `CREATE DATABASE` / `USE`
    - Ajouter `IF NOT EXISTS`
    - Entourer les noms par des backticks

---

## Contenu du script

### Initialisation
- Configuration du mode SQL
- Définition du fuseau horaire
- Encodage `utf8mb4`

### Création des tables
Création (avec suppression préalable) des tables :

| Table                         | Rôle                                           |
| ----------------------------- | ---------------------------------------------- |
| `categorie`                   | Référentiel des catégories de formation        |
| `playlist`                    | Référentiel des playlists YouTube              |
| `formation`                   | Liste des vidéos/formations                    |
| `formation_categorie`         | Association N-N entre formations et catégories |
| `doctrine_migration_versions` | Historique des migrations Doctrine             |
| `messenger_messages`          | Table utilisée par Symfony Messenger           |

### Données

#### Catégories

Le script crée 9 catégories :

| ID | Catégorie |
| -- | --------- |
| 1  | Java      |
| 2  | UML       |
| 3  | C#        |
| 4  | Python    |
| 5  | MCD       |
| 6  | Android   |
| 7  | POO       |
| 8  | SQL       |
| 9  | Cours     |

Ces catégories sont utilisées pour filtrer et classer les formations.

---

#### Playlists

Le script insère 26 playlists pédagogiques.

Exemples :

* Eclipse et Java
* Visual Studio 2019 et C#
* Programmation sous Python
* TP Android
* POO TP Java
* Cours UML
* Cours Merise/2
* Cours Modèle relationnel et MCD
* Cours de programmation objet
* Plusieurs corrections de sujets BTS SIO (RESTILOC, LOCALUX, WEBCAISSE, DANE, AHM-23, etc.)

Chaque playlist contient une description détaillée permettant d'identifier rapidement son contenu.

---

#### Formations

Chaque formation possède :

* un identifiant ;
* une playlist d'appartenance ;
* une date de publication ;
* un titre ;
* une description ;
* l'identifiant de la vidéo YouTube (`video_id`).

Le `video_id` permet de reconstruire directement l'URL de la vidéo :

```text
https://www.youtube.com/watch?v=<video_id>
```

Exemple :

```text
https://www.youtube.com/watch?v=Z4yTTXka958
```

---

#### Relations formation ↔ catégorie

La table `formation_categorie` contient des associations : une formation peut appartenir à plusieurs catégories simultanément.

Exemples :

* une vidéo peut être classée à la fois en **Java** et **POO** ;
* une vidéo peut être classée en **SQL** et **Cours** ;
* une vidéo Android peut également être classée comme **Cours**.

Cette table matérialise la relation **many-to-many** entre les entités.

---

#### Historique des migrations Doctrine

Le script insère les versions de migration déjà exécutées dans :

```sql
doctrine_migration_versions
```

Cela permet à Doctrine de considérer la base comme déjà migrée lors de son import.

Versions présentes :

* `Version20240513134621`
* `Version20260323201732`

#### Attention

Si de nouvelles migrations sont créées après la génération de ce fichier, le contenu de cette table pourra devenir obsolète.

Dans ce cas :

* soit régénérer le script ;
* soit laisser Doctrine reconstruire l'historique via les migrations.


---

## 🚀 Utilisation

### Via phpMyAdmin
1. Créer la base `mediatekformation`
2. Importer `seed.sql`

### Via la ligne de commande

```bash
mysql -u utilisateur -p mediatekformation < sql/seed.sql
```

Ce fichier est également utilisé par des scripts d’initialisation avant le lancement de l’application Symfony.

---

## Attention
Le script **supprime les tables existantes** (`DROP TABLE IF EXISTS`).

---


Ce README est destiné à expliquer le contenu et l’utilisation du fichier `seed.sql` pour les développeurs travaillant sur le projet Symfony.

---

# 📦 Seed SQL – Base de données `mediatekformation`

Le fichier `seed.sql` est un export phpMyAdmin utilisé pour **initialiser la base MySQL** du projet Symfony avec une structure propre et des données prêtes à l’emploi.

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

## 📚 Contenu du script

### 1. 🔧 Initialisation
- Configuration du mode SQL
- Définition du fuseau horaire
- Encodage `utf8mb4`

### 2. 🗃️ Création des tables
Création (avec suppression préalable) des tables :

- **`categorie`** — catégories de formations
- **`formation`** — vidéos de formation (titre, description, date, ID YouTube, playlist)
- **`doctrine_migration_versions`** — historique des migrations Doctrine

### 3. 🌱 Insertion des données
- 📂 Catégories (Java, Python, SQL, UML, etc.)
- 🎓 Formations (titres, descriptions, IDs YouTube réalistes)
- ⚙️ Versions de migrations Doctrine

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

## ⚠️ Attention
Le script **supprime les tables existantes** (`DROP TABLE IF EXISTS`).

---

## 📁 Emplacement

```
sql/
 ├── seed.sql
 └── README.md
```

# Atelier 1 - Plan des missions

Plan suivi pour éxecuter les missions à réaliser dans le cadre de l'atelier 1 dans le cadre de la formation SIO Solutions Logicielles et Applications Métiers.

## Objectifs

Corriger et faire évoluer une application web (Symfony) exploitant une base de données relationnelle MySQL et qui met à disposition des vidéos d'auto-formation en ligne proposées par MediaTek86 (contexte détaillé présenté ci-après).

---

## Sommaire

- Mission 1 – Nettoyer et optimiser le code existant
- Mission 2 – Coder la partie back-office
- Mission 3 – Tester et documenter
- Mission 4 – Déployer le site et gérer le déploiement continu

---

## Mission 1 – Nettoyer et optimiser le code existant

### Tâche 1 : nettoyer le code
Nettoyer le code en suivant les indications de Sonarlint (ne nettoyer que les fichiers créés par le développeur.
En rappel :
- Éviter les chaînes "en dur" (pour éliminer les "strings literals duplicated").
- Nommer les constantes en majuscule.
- Fusionner certains tests imbriqués inutilement.
- Ajouter l'attribut "alt" à toutes les images.
- Ajouter l'attribut "description" à toutes les tables.

### Tâche 2 : ajouter une fonctionnalité
Dans la page des playlists, ajouter une colonne pour afficher le nombre de formations par playlist et permettre le tri croissant et décroissant sur cette colonne. Cette information doit aussi s'afficher dans la page d'une playlist.

---

## Mission 2 – Coder la partie back-office

Le back office doit permettre de gérer le contenu de la base de données.
Il doit contenir la même bannière que le front office et un menu contenant "Formations", "Playlists" et "Catégories".
L'architecture doit respecter celle du front office, avec le respect de la qualité de code.

Dans le code ajouté, tout doit être mis en place pour sécuriser le site :
- requêtes paramétrées pour éviter les injections SQL ;
- token pour contrer la CSRF ;
- les contrôles de saisie.

### Tâche 1 : gérer les formations
- Une page doit permettre de lister les formations et, pour chaque formation, afficher un bouton permettant de la supprimer (après confirmation) et un bouton permettant de la modifier.
- Si une formation est supprimée, il faut aussi l'enlever de la playlist où elle se trouvait.
- Les mêmes tris et filtres présents dans le front office doivent être présents dans le back office.
- Un bouton doit permettre d'accéder au formulaire d'ajout d'une formation. Les saisies doivent être contrôlées. Seul le champ "description" n'est pas obligatoire ainsi que la sélection de catégories (une formation peut n'avoir aucune catégorie). La playlist et la ou les catégories doivent être sélectionnées dans une liste (une seule playlist par formation, plusieurs catégories possibles par formation). La date ne doit pas être saisie mais sélectionnée. Elle ne doit pas être postérieure à la date du jour.
- Le clic sur le bouton permettant de modifier une formation doit amener sur le même formulaire, mais cette fois prérempli.

### Tâche 2 : gérer les playlists
- Une page doit permettre de lister les playlists et, pour chaque playlist, afficher un bouton permettant de la supprimer (après confirmation) et un bouton permettant de la modifier.
- La suppression d'une playlist n'est possible que si aucune formation n'est rattachée à elle.
- Les mêmes tris et filtres présents dans le front office doivent être présents dans le back office.
- Un bouton doit permettre d'accéder au formulaire d'ajout d'une playlist. Les saisies doivent être contrôlées. L'ajout d'une playlist consiste juste à saisir son nom et sa description. Seul le champ name est obligatoire.
- Le clic sur le bouton permettant de modifier une playlist doit amener sur le même formulaire, mais cette fois prérempli. Cette fois, la liste des formations de la playlist doit apparaître, mais il ne doit pas être possible d'ajouter ou de supprimer une formation : ce n'est que dans le formulaire de la formation qu'il est possible de préciser sa playlist de rattachement.

### Tâche 3 : gérer les catégories
- Une page doit permettre de lister les catégories et, pour chaque catégorie, afficher un bouton permettant de la supprimer. Attention, une catégorie ne peut être supprimée que si elle n'est rattachée à aucune formation.
- Dans la même page, un mini formulaire doit permettre de saisir et d'ajouter directement une nouvelle catégorie, à condition que le nom de la catégorie n'existe pas déjà.

### Tâche 4 : ajouter l'accès avec authentification
- Le back office ne doit être accessible qu'après authentification : un seul profil administrateur doit avoir le droit d’accès.
- L'accès au back office doit se faire en ajoutant "/admin" à la fin de l'URL.
- Il doit être possible de se déconnecter, sur toutes les pages (avec un lien de déconnexion).

---

## Mission 3 – Tester et documenter

### Tâche 1 : gérer les tests
**Tests unitaires :**
Contrôler le fonctionnement de la méthode qui retourne la date de parution au format string.
**Tests d'intégration sur les règles de validation :**
Lors de l'ajout ou de la modification d'une formation, contrôler que la date n'est pas postérieure à aujourd'hui.
**Tests d'intégration sur les Repository :**
Contrôler toutes les méthodes ajoutées dans les classes Repository (pour cela, créer une BDD de test).
**Tests fonctionnels :**
Contrôler que la page d'accueil est accessible.
Dans chaque page contenant des listes :
contrôler que les tris fonctionnent (en testant juste le résultat de la première ligne) ;
contrôler que les filtres fonctionnent (en testant le nombre de lignes obtenu et le résultat de la première ligne) ;
contrôler que le clic sur un lien (ou bouton) dans une liste permet d'accéder à la bonne page (en contrôlant l'accès à la page mais aussi le contenu d'un des éléments de la page).
**Tests de compatibilité :**
Tester le site sur plusieurs navigateurs pour contrôler la compatibilité.

### Tâche 2 : créer la documentation technique
- Contrôler que tous les commentaires normalisés nécessaires à la génération de la documentation technique ont été correctement insérés.
- Générer la documentation technique du site complet : front et back office excluant le code automatiquement généré par Symfony (voir l'article "Génération de la documentation technique sous NetBeans" dans le wiki du dépôt).

### Tâche 3 : créer la documentation utilisateur
Créer en vidéo qui permet de montrer toutes les fonctionnalités du site (front et back office).
Cette vidéo ne doit pas dépasser les 5mn et doit présenter clairement toutes les fonctionnalités, en montrant les manipulations qui doivent être accompagnées d'explications orales.

---

## Mission 4 – Déployer le site et gérer le déploiement continu

### Tâche 1 : déployer le site
Déployer le site, la BDD et la documentation technique chez un hébergeur.
Mettre à jour la page de CGU avec la bonne adresse du site.

### Tâche 2 : gérer la sauvegarde et la restauration de la BDD
Une sauvegarde journalière automatisée doit être programmée pour la BDD (voir l'article "Automatiser la sauvegarde d'une BDD" dans le wiki du dépôt).
La restauration pourra se faire manuellement, en exécutant le script de sauvegarde.

### Tâche 3 : mettre en place le déploiement continu
Configurer le dépôt Github pour que le site en ligne soit mis à jour à chaque push reçu dans le dépôt.

---
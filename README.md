# Mediatekformation
## Présentation
Ce site, développé avec Symfony 6.4 sur la base du code de [ce dépot](https://github.com/CNED-SLAM/mediatekformation), permet d'accéder aux vidéos d'auto-formation proposées par une chaîne de médiathèques et qui sont aussi accessibles sur YouTube.<br> 
Vous pouvez consulter le site [ici](https://testhebergement.go.yj.fr)

![img1](https://github.com/user-attachments/assets/9c5c503b-738d-40cf-ba53-36ba4c0209e8)
## Les différentes pages
Voici les 5 pages correspondant aux différents cas d’utilisation.
### Page 1 : l'accueil
Cette page présente le fonctionnement du site et les 2 dernières vidéos mises en ligne.<br>
La partie du haut contient une bannière (logo, nom et phrase présentant le but du site) et le menu permettant d'accéder aux 3 pages principales (Accueil, Formations, Playlists).<br>
Le centre contient un texte de présentation avec, entre autres, les liens pour accéder aux 2 autres pages principales.<br>
La partie basse contient les 2 dernières formations mises en ligne. Cliquer sur une image permet d'accéder à la page 3 de présentation de la formation.<br>
Le bas de page contient un lien pour accéder à la page des CGU : ce lien est présent en bas de chaque page excepté la page des CGU.<br>
![img2](https://github.com/user-attachments/assets/523b4233-3505-4b8c-9db0-5e7b72965bc6)
### Page 2 : les formations
Cette page présente les formations proposées en ligne (accessibles sur YouTube).<br>
La partie haute est identique à la page d'accueil (bannière et menu).<br>
La partie centrale contient un tableau composé de 5 colonnes :<br>
•	La 1ère colonne ("formation") contient le titre de chaque formation.<br>
•	La 2ème colonne ("playlist") contient le nom de la playlist dans laquelle chaque formation se trouve.<br>
•	La 3ème colonne ("catégories") contient la ou les catégories concernées par chaque formation (langage…).<br>
•	La 4ème colonne ("date") contient la date de parution de chaque formation.<br>
•	LA 5ème contient la capture visible sur YouTube, pour chaque formation.<br>
Au niveau des colonnes "formation", "playlist" et "date", 2 boutons permettent de trier les lignes en ordre croissant ("<") ou décroissant (">").<br>
Au niveau des colonnes "formation" et "playlist", il est possible de filtrer les lignes en tapant un texte : seuls les lignes qui contiennent ce texte sont affichées. Si la zone est vide, le fait de cliquer sur "filtrer" permet de retrouver la liste complète.<br> 
Au niveau de la catégorie, la sélection d'une catégorie dans le combo permet d'afficher uniquement les formations qui ont cette catégorie. Le fait de sélectionner la ligne vide du combo permet d'afficher à nouveau toutes les formations.<br>
Par défaut la liste est triée sur la date par ordre décroissant (la formation la plus récente en premier).<br>
Le fait de cliquer sur une miniature permet d'accéder à la troisième page contenant le détail de la formation.<br>
![img3](https://github.com/user-attachments/assets/bc033cf9-41a5-4cad-a268-8abb400965c5)
### Page 3 : détail d'une formation
Cette page n'est pas accessible par le menu mais uniquement en cliquant sur une miniature dans la page "Formations" ou une image dans la page "Accueil".<br>
La partie haute est identique à la page d'accueil (bannière et menu).<br>
La partie centrale est séparée en 2 parties :<br>
•	La partie gauche contient la vidéo qui peut être directement visible dans le site ou sur YouTube.<br>
•	La partie droite contient la date de parution, le titre de la formation, le nom de la playlist, la liste des catégories et sa description détaillée.<br>
![img4](https://github.com/user-attachments/assets/f41d05d8-5980-4dc4-9eb7-58d1c31b8a25)
### Page 4 : les playlists
Cette page présente les playlists.<br>
La partie haute est identique à la page d'accueil (bannière et menu).<br>
La partie centrale contient un tableau composé de 3 colonnes :<br>
•	La 1ère colonne ("playlist") contient le nom de chaque playlist.<br>
•	La 2ème colonne ("catégories") contient la ou les catégories concernées par chaque playlist (langage…).<br>
•	La 3ème contient un bouton pour accéder à la page de présentation de la playlist.<br>
Au niveau de la colonne "playlist", 2 boutons permettent de trier les lignes en ordre croissant ("<") ou décroissant (">"). Il est aussi possible de filtrer les lignes en tapant un texte : seuls les lignes qui contiennent ce texte sont affichées. Si la zone est vide, le fait de cliquer sur "filtrer" permet de retrouver la liste complète.<br> 
Au niveau de la catégorie, la sélection d'une catégorie dans le combo permet d'afficher uniquement les playlists qui ont cette catégorie. Le fait de sélectionner la ligne vide du combo permet d'afficher à nouveau toutes les playlists.<br>
Par défaut la liste est triée sur le nom de la playlist.<br>
Cliquer sur le bouton "voir détail" d'une playlist permet d'accéder à la page 5 qui présente le détail de la playlist concernée.<br>
![img5](https://github.com/user-attachments/assets/bbe8934f-8d4b-4da2-8216-60b96b726d8a)
### Page 5 : détail d'une playlist
Cette page n'est pas accessible par le menu mais uniquement en cliquant sur un bouton "voir détail" dans la page "Playlists".<br>
La partie haute est identique à la page d'accueil (bannière et menu).<br>
La partie centrale est séparée en 2 parties :<br>
•	La partie gauche contient les informations de la playlist (titre, liste des catégories, description).<br>
•	La partie droite contient la liste des formations contenues dans la playlist (miniature et titre) avec possibilité de cliquer sur une formation pour aller dans la page de la formation.<br>
![img6](https://github.com/user-attachments/assets/f216a9e7-084a-4683-9b4e-cada5574a0e2)

<img width="1318" height="956" alt="schema-admin" src="https://github.com/user-attachments/assets/1c66eb9a-fb75-44ca-9e92-9c129d1414d0" />

### Page 6 : gestion des formations

Version admin des formations vue plus haut, mais avec en plus la possibilité de créer, modifier ou supprimer une formation. 
La création ou la modification d'une formation se fait à l'aide d'un formulaire qui permet de saisir les différentes informations (titre, Video ID, description, date de parution, playlist, catégories).

**Accessible uniquement à l'administrateur**

<img width="1688" height="865" alt="admin-formation" src="https://github.com/user-attachments/assets/a38598e9-ab64-4e24-aebf-26a83062b353" />

### Page 7 : gestion des playlists

Version admin des playlists vue plus haut, mais avec en plus la possibilité de créer, modifier ou supprimer une playlist. 
La création ou la modification d'une playlist se fait à l'aide d'un formulaire qui permet de saisir les différentes informations (nom, description, catégories).

**Accessible uniquement à l'administrateur**

<img width="1681" height="861" alt="admin-playlist" src="https://github.com/user-attachments/assets/95a4c6c8-2ac3-47e2-9aa4-3c42998766c4" />

### Page 8 : gestion des catégories

Version admin des catégories vue plus haut, mais avec en plus la possibilité de créer ou supprimer une catégorie. 
La création d'une catégorie se fait à l'aide d'un formulaire qui permet de saisir les différentes informations (nom).

**Accessible uniquement à l'administrateur**

<img width="1684" height="864" alt="admin-categorie" src="https://github.com/user-attachments/assets/872638d4-c6a3-4031-b018-501c1eee0317" />

### Formulaires
Voici les différents formulaires utilisés pour la création ou la modification d'une formation, d'une playlist ou d'une catégorie.

- Formulaire de création ou de modification d'une formation
<img width="1676" height="848" alt="form-creationformation" src="https://github.com/user-attachments/assets/a2e7ab14-a561-49ed-9627-7cfbf000e9ba" />

- Formulaire de création ou de modification d'une playlist
<img width="1703" height="862" alt="form-creationplaylist" src="https://github.com/user-attachments/assets/0fde3fec-767e-49e8-8870-f0ef9d2ee870" />

- Formulaire de login
<img width="1686" height="868" alt="form-login" src="https://github.com/user-attachments/assets/81122a1d-d4f2-4002-b654-060f61986f5c" />

## La base de données
La base de données exploitée par le site est au format MySQL.
### Schéma conceptuel de données
Voici le schéma correspondant à la BDD.<br>
![img7](https://github.com/user-attachments/assets/f3eca694-bf96-4f6f-811e-9d11a7925e9e)
<br>video_id contient le code YouTube de la vidéo, qui permet ensuite de lancer la vidéo à l'adresse suivante :<br>
https://www.youtube.com/embed/<<<video_id>>>
### Relations issues du schéma
<code><strong>formation (id, published_at, title, video_id, description, playlist_id)</strong>
id : clé primaire
playlist_id : clé étrangère en ref. à id de playlist
<strong>playlist (id, name, description)</strong>
id : clé primaire
<strong>categorie (id, name)</strong>
id : clé primaire
<strong>formation_categorie (id_formation, id_categorie)</strong>
id_formation, id_categorie : clé primaire
id_formation : clé étrangère en ref. à id de formation
id_categorie : clé étrangère en ref. à id de categorie</code>

Remarques : 
Les clés primaires des entités sont en auto-incrémentation.<br>
Le chemin des images (des 2 tailles) n'est pas mémorisé dans la BDD car il peut être fabriqué de la façon suivante :<br>
"https://i.ytimg.com/vi/" suivi de, soit "/default.jpg" (pour la miniature), soit "/hqdefault.jpg" (pour l'image plus grande de la page d'accueil).
## Test de l'application en local
- Vérifier que Composer, Git et Wamserver (ou équivalent) sont installés sur l'ordinateur.
- Télécharger le code et le dézipper dans www de Wampserver (ou dossier équivalent) puis renommer le dossier en "mediatekformation".<br>
- Ouvrir une fenêtre de commandes en mode admin, se positionner dans le dossier du projet et taper "composer install" pour reconstituer le dossier vendor.<br>
- Dans phpMyAdmin, se connecter à MySQL en root sans mot de passe et créer la BDD 'mediatekformation'.<br>
- Récupérer le fichier mediatekformation.sql en racine du projet et l'utiliser pour remplir la BDD (si vous voulez mettre un login/pwd d'accès, il faut créer un utilisateur, lui donner les droits sur la BDD et il faut le préciser dans le fichier ".env" en racine du projet).<br>
- De préférence, ouvrir l'application dans un IDE professionnel. L'adresse pour la lancer est : http://localhost/mediatekformation/public/index.php<br>

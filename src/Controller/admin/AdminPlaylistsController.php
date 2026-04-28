<?php

namespace App\Controller\admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur pour la gestion des playlists par l'administrateur.
 */
class AdminPlaylistsController extends AbstractController
{
    /**
     * Chemin du template Twig pour la gestion des playlists.
     */
    private const TWIG_ADMIN_PLAYLISTS = 'admin/admin.playlists.html.twig';

    /**
     * Repository des playlists.
     */
    private PlaylistRepository $playlistRepository;

    /**
     * Repository des formations.
     */
    private FormationRepository $formationRepository;

    /**
     * Repository des catégories.
     */
    private CategorieRepository $categorieRepository;

    /**
     * Constructeur du contrôleur.
     *
     * @param PlaylistRepository $playlistRepository Repository des playlists
     * @param CategorieRepository $categorieRepository Repository des catégories
     * @param FormationRepository $formationRepository Repository des formations
     */
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }
    
    /**
     * Affiche la liste des playlists.
     *
     * @return Response Réponse HTTP
     */
    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TWIG_ADMIN_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * Trie les playlists selon un champ et un ordre donnés.
     *
     * @param string $champ Champ de tri
     * @param string $ordre Ordre de tri (ASC ou DESC)
     * @param string $table Table associée (optionnel)
     * @return Response Réponse HTTP
     */
    #[Route('/admin/playlists/tri/{champ}/{ordre}/{table}', name: 'admin.playlists.sort')]
    public function sort(string $champ, string $ordre, string $table = ""): Response
    {
        if (!in_array(strtoupper($ordre), ['ASC', 'DESC'])) {
            $ordre = 'ASC';
        }
        
        switch ($champ) {
            case 'name':
                // Si tri demandé sur le nom des playlists
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;

            case 'nbFormations':
                // Si tri demandé sur le nombre de formations
                $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
                break;

            default:
                // Champ inconnu → tri par nom croissant
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;
        }
        
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TWIG_ADMIN_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * Recherche des playlists contenant une valeur dans un champ donné.
     *
     * @param string $champ Champ de recherche
     * @param Request $request Requête HTTP
     * @param string $table Table associée (optionnel)
     * @return Response Réponse HTTP
     */
    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TWIG_ADMIN_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * Ajoute une nouvelle playlist.
     *
     * @param Request $request Requête HTTP
     * @return Response Réponse HTTP
     */
    #[Route('/admin/playlist/ajout', name: 'admin.playlist.ajout')]
    public function ajout(Request $request): Response
    {
        $playlist = new Playlist();
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        
        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            $this->playlistRepository->add($playlist);
            return $this->redirectToRoute('admin.playlists');
        }
        return $this->render("admin/admin.playlist.ajout.html.twig", [
            'playlist' => $playlist,
            'formPlaylist' => $formPlaylist->createView()
        ]);
    }
    
    /**
     * Modifie une playlist existante.
     *
     * @param int $id Identifiant de la playlist
     * @param Request $request Requête HTTP
     * @return Response Réponse HTTP
     */
    #[Route('/admin/playlist/edit/{id}', name: 'admin.playlist.edit')]
    public function edit(int $id, Request $request): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        
        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            $this->playlistRepository->add($playlist);
            //Redirect vers la liste des playlists
            return $this->redirectToRoute('admin.playlists');
        }
        
        // On récupère toutes les formations liées à la playlist
        $formations = $this->formationRepository->findAllForOnePlaylist($id);
        
        return $this->render("admin/admin.playlist.edit.html.twig", [
            'playlist' => $playlist,
            'formations' => $formations,
            'formPlaylist' => $formPlaylist->createView()
        ]);
    }
    
    /**
     * Supprime une playlist si elle ne contient aucune formation.
     *
     * @param int $id Identifiant de la playlist
     * @return Response Réponse HTTP
     */
    #[Route('/admin/playlist/suppr/{id}', name: 'admin.playlist.suppr')]
    public function suppr(int $id) : Response
    {
        // On récupère toutes les formations liées à la playlist
        $formations = $this->formationRepository->findAllForOnePlaylist($id);
        if (!empty($formations)) {
            $this->addFlash('danger', 'Impossible de supprimer une playlist contenant des formations.');
            return $this->redirectToRoute('admin.playlists');
        }
        
        $playlist = $this->playlistRepository->find($id);
        $this->playlistRepository->remove($playlist);
        $this->addFlash('success', 'La playlist a été supprimée.');
        return $this->redirectToRoute('admin.playlists');
    }
}

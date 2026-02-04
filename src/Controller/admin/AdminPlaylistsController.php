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
 * Controleur pour la gestion des playlists par l'admin
 *
 */
class AdminPlaylistsController extends AbstractController
{
    private const TWIG_ADMIN_PLAYLISTS = 'admin/admin.playlists.html.twig';

    private PlaylistRepository $playlistRepository;
    private FormationRepository $formationRepository;
    private CategorieRepository $categorieRepository;

    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }
    
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

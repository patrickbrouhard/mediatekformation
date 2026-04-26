<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur des playlists.
 */
class PlaylistsController extends AbstractController
{
    /**
     * Chemin du template Twig pour la liste des playlists.
     */
    private const PAGE_PLAYLISTS = 'pages/playlists.html.twig';

    /**
     * Chemin du template Twig pour le détail d'une playlist.
     */
    private const PAGE_PLAYLIST = 'pages/playlist.html.twig';

    /**
     * Repository des playlists.
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     * Repository des formations.
     *
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * Repository des catégories.
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     * Constructeur du contrôleur.
     *
     * @param PlaylistRepository $playlistRepository Repository des playlists
     * @param CategorieRepository $categorieRepository Repository des catégories
     * @param FormationRepository $formationRespository Repository des formations
     */
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRespository
            ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * Affiche la liste des playlists.
     *
     * @return Response Réponse HTTP
     */
    #[Route('/playlists', name: 'playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * Trie les playlists selon un champ et un ordre donnés.
     *
     * @param string $champ Champ de tri
     * @param string $ordre Ordre de tri (ASC ou DESC)
     * @return Response Réponse HTTP
     */
    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
    public function sort(string $champ, string $ordre): Response
    {
        $ordre = strtoupper($ordre) === 'DESC' ? 'DESC' : 'ASC';
        
        if ($champ === 'name') {
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        } elseif ($champ === 'nbFormations') {
            $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
        } else {
            $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        }
        
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
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
    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * Affiche le détail d'une playlist.
     *
     * @param int $id Identifiant de la playlist
     * @return Response Réponse HTTP
     */
    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render(self::PAGE_PLAYLIST, [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }
}


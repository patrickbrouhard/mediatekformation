<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur des formations.
 */
class FormationsController extends AbstractController
{
    /**
     * Chemin du template Twig pour la liste des formations.
     */
    private const TWIG_FORMATIONS = 'pages/formations.html.twig';

    /**
     * Chemin du template Twig pour le détail d'une formation.
     */
    private const TWIG_FORMATION = 'pages/formation.html.twig';

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
     * @param FormationRepository $formationRepository Repository des formations
     * @param CategorieRepository $categorieRepository Repository des catégories
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * Affiche la liste des formations.
     *
     * @return Response Réponse HTTP
     */
    #[Route('/formations', name: 'formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TWIG_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Trie les formations selon un champ et un ordre donnés.
     *
     * @param string $champ Champ de tri
     * @param string $ordre Ordre de tri (ASC ou DESC)
     * @param string $table Table associée (optionnel)
     * @return Response Réponse HTTP
     */
    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort($champ, $ordre, $table=""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TWIG_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Recherche des formations contenant une valeur dans un champ donné.
     *
     * @param string $champ Champ de recherche
     * @param Request $request Requête HTTP
     * @param string $table Table associée (optionnel)
     * @return Response Réponse HTTP
     */
    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TWIG_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * Affiche le détail d'une formation.
     *
     * @param int $id Identifiant de la formation
     * @return Response Réponse HTTP
     */
    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render(self::TWIG_FORMATION, [
            'formation' => $formation
        ]);
    }
    
}

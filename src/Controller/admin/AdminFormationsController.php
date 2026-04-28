<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur pour la gestion des formations par l'administrateur.
 */
class AdminFormationsController extends AbstractController
{
    /**
     * Chemin du template Twig pour la gestion des formations.
     */
    private const TWIG_ADMIN_FORMATIONS = 'admin/admin.formations.html.twig';

    /**
     * Repository des formations.
     *
     * @var FormationRepository
     */
    private FormationRepository $formationRepository;

    /**
     * Repository des catégories.
     *
     * @var CategorieRepository
     */
    private CategorieRepository $categorieRepository;

    /**
     * Constructeur du contrôleur.
     *
     * @param FormationRepository $formationRepository Repository des formations
     * @param CategorieRepository $categorieRepository Repository des catégories
     */
    public function __construct(
        FormationRepository $formationRepository,
        CategorieRepository $categorieRepository
    ) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * Affiche la liste des formations.
     *
     * @return Response Réponse HTTP
     */
    #[Route('/admin/formations', name: 'admin.formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TWIG_ADMIN_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * Trie les formations selon un champ et un ordre donnés.
     *
     * @param string $champ Champ de tri
     * @param string $ordre Ordre de tri (ASC ou DESC)
     * @param string $table Table associée pour le tri (optionnel)
     * @return Response Réponse HTTP
     */
    #[Route('/admin/formations/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sort($champ, $ordre, $table=""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TWIG_ADMIN_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * Recherche des formations contenant une valeur dans un champ donné.
     *
     * @param string $champ Champ de recherche
     * @param Request $request Requête HTTP
     * @param string $table Table associée pour la recherche (optionnel)
     * @return Response Réponse HTTP
     */
    #[Route('/admin/formations/recherche/{champ}/{table}', name: 'admin.formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TWIG_ADMIN_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * Ajoute une nouvelle formation.
     *
     * @param Request $request Requête HTTP
     * @return Response Réponse HTTP
     */
    #[Route('/admin/formation/ajout', name: 'admin.formation.ajout')]
    public function ajout(Request $request): Response
    {
        $formation = new Formation();
        $formFormation = $this->createForm(FormationType::class, $formation);
        
        $formFormation->handleRequest($request);
        if ($formFormation->isSubmitted() && $formFormation->isValid()) {
            $this->formationRepository->add($formation);
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render("admin/admin.formation.ajout.html.twig", [
            'formation' => $formation,
            'formFormation' => $formFormation->createView()
        ]);
    }
    
    /**
     * Modifie une formation existante.
     *
     * @param int $id Identifiant de la formation
     * @param Request $request Requête HTTP
     * @return Response Réponse HTTP
     */
    #[Route('/admin/formation/edit/{id}', name: 'admin.formation.edit')]
    public function edit(int $id, Request $request): Response
    {
        $formation = $this->formationRepository->find($id);
        $formFormation = $this->createForm(FormationType::class, $formation);
        
        $formFormation->handleRequest($request);
        if ($formFormation->isSubmitted() && $formFormation->isValid()) {
            $this->formationRepository->add($formation);
            //Redirect vers la liste des formations
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render("admin/admin.formation.edit.html.twig", [
            'formation' => $formation,
            'formFormation' => $formFormation->createView()
        ]);
    }
    
    /**
     * Supprime une formation.
     *
     * @param int $id Identifiant de la formation
     * @return Response Réponse HTTP
     */
    #[Route('/admin/formation/suppr/{id}', name: 'admin.formation.suppr')]
    public function suppr(int $id) : Response
    {
        $formation = $this->formationRepository->find($id);
        $this->formationRepository->remove($formation);
        return $this->redirectToRoute('admin.formations');
    }
}

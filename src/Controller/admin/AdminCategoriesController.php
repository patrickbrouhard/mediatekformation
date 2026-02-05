<?php

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Form\CategorieType;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controleur pour la gestion des categories par l'admin
 *
 */
class AdminCategoriesController extends AbstractController
{
    private const TWIG_ADMIN_CATEGORIES = 'admin/admin.categories.html.twig';

    private CategorieRepository $categorieRepository;
    private FormationRepository $formationRepository;

    public function __construct(
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository
    ) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
    
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(Request $request): Response
    {
        $categories = $this->categorieRepository->findAll();
        
        $nouvelleCategorie = new Categorie();
        $formCategorie = $this->createForm(CategorieType::class, $nouvelleCategorie);
        $formCategorie->handleRequest($request);
        if ($formCategorie->isSubmitted() && $formCategorie->isValid()) {
            // pas de vérification de doublon, c'est géré dans l'entité
            // avec contrainte SQL (unique: true) et Symfony Validator (UniqueEntity)
            $this->categorieRepository->add($nouvelleCategorie);
            $this->addFlash('success', 'Catégorie ajoutée.');
            return $this->redirectToRoute('admin.categories');
        }
        
        return $this->render(self::TWIG_ADMIN_CATEGORIES, [
            'categories' => $categories,
            'formCategorie' => $formCategorie->createView(),
        ]);
    }
        
    #[Route('/admin/categorie/suppr/{id}', name: 'admin.categorie.suppr')]
    public function suppr(int $id) : Response
    {
        $categorie = $this->categorieRepository->find($id);
        // gestion obligatoire du cas où l'id n'existe pas (risque de crash)
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        if ($categorie->getFormations()->isEmpty()) {
            $this->categorieRepository->remove($categorie);
            $this->addFlash('success', 'La catégorie a été supprimée.');
        } else {
            $this->addFlash('danger', 'Impossible de supprimer une catégorie rattachée à des formations.');
        }
        
        return $this->redirectToRoute('admin.categories');
    }
}

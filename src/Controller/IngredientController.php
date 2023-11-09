<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Symfony\Component\HttpFoundation\Request; // Assurez-vous d'importer correctement la classe Request
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient')]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
    
        // $ingredients = $repository->findAll();
        // dd($ingredients);
        return $this->render('pages/ingredient/index.html.twig', [
            // 'controller_name' => 'IngredientController',
            'ingredients'=> $ingredients
        ]);
    }
}

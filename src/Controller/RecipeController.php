<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * This controller display all recipes
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette', name: 'recipe.index', methods:['GET'])]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
    /**
     * This controller show us to create a new recipe 
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */

    #[Route('/recette/creation', 'recipe.new', methods:['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $manager):Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // dd($form->getData());
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créé avec succés !'
            );

            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/new.html.twig', [
         'form' => $form->createView()
        ]);
    }
    
    /**
     * This controller show us to edite a new recipe 
     *
     * @param Recipe $recipe
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/edition/{id}', name:'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $manager) : Response
    {
        // dd($ingredient);
        // $ingredient = $repository->findOneBy(["id"=>$id]);
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            // $task = $form->getData();
            // dd($form->getData());
            $recipe=$form->getData();
            // dd($ingredient);
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre recette a été modifie avec succés !'
            );
            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/edit.html.twig', ['form' => $form->createView()]);
    }
    
    /**
     * This controller show us to delete a recipe 
     *
     * @param EntityManagerInterface $manager
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('/recette/delete/{id}', name: 'recipe.delete', methods: ['GET', 'POST'])]
    public function delete(EntityManagerInterface $manager, Recipe $recipe):Response
    {
        if (!$recipe) {
            $this->addFlash(
                'success',
                'L\'recette en question n\'a pas été trouvé !'
            );
            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('recipe.index');
        }
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre recette a été supprimé avec succés !'
        );
        return $this->redirectToRoute('recipe.index');
    }
}

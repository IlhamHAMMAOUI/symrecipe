<?php

namespace App\Controller;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\IngredientType;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Assurez-vous d'importer correctement la classe Request

class IngredientController extends AbstractController
{
    /**
     * This controller display all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'app_ingredient', methods:['GET'])]
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
    /**
     * This controller show a form which create an ingredient 
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', name: 'ingredient.new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager):Response
    {
        $ingredient = new Ingredient();
        // $task->setTask('Write a blog post');
        // $task->setDueDate(new \DateTimeImmutable('tomorrow'));

        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            // $task = $form->getData();
            // dd($form->getData());
            $ingredient=$form->getData();
            // dd($ingredient);
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingrédient a été créé avec succés !'
            );
            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('app_ingredient');
        }


        return $this->render('pages/ingredient/new.html.twig', [
            'form'=>$form->createView()
        ]);
    }
    #[Route('/ingredient/edition/{id}', name:'ingredient.edit', methods: ['GET', 'POST'])]
    // #[ParamConverter('ingredient', class: 'App\Entity\Ingredient')]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $manager) : Response
    {
        // dd($ingredient);
        // $ingredient = $repository->findOneBy(["id"=>$id]);
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            // $task = $form->getData();
            // dd($form->getData());
            $ingredient=$form->getData();
            // dd($ingredient);
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingrédient a été modifie avec succés !'
            );
            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('app_ingredient');
        }
        return $this->render('pages/ingredient/edit.html.twig', ['form' => $form->createView()]);
    }
    #[Route('/ingredient/delete/{id}', name: 'ingredient.delete', methods: ['GET', 'POST'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient):Response
    {
        if (!$ingredient) {
            $this->addFlash(
                'success',
                'L\'ingrédient en question n\'a pas été trouvé !'
            );
            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('app_ingredient');
        }
        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succés !'
        );
        return $this->redirectToRoute('app_ingredient');
    }
}

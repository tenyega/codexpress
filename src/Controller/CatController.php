<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cat')]
final class CatController extends AbstractController
{
    #[Route(name: 'app_cat_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('cat/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_cat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cat/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cat_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('cat/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cat/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cat_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cat_index', [], Response::HTTP_SEE_OTHER);
    }
}

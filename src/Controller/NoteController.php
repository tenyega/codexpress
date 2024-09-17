<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/notes')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_all', methods: ['GET'])]
    public function all(NoteRepository $nr): Response
    {

        /**
         * Get the collection of all the notes that are public and in descending order which is the most recently create first 
         */
        $all_notes = $nr->findBy(['is_public' => true], ['created_at' => 'DESC']);
        return $this->render('note/all.html.twig', [
            'all_notes' => $all_notes,
        ]);
    }
    #[IsGranted('IS_AUTHENTICATED_FULLY')]

    #[Route('/n/{slug}', name: 'app_note_show')]
    public function show(NoteRepository $nr, string $slug): Response
    {

        /**
         * Get the collection of all the notes that are public and in descending order which is the most recently create first 
         */
        $note = $nr->findOneBy(['slug' => $slug]);
        // $note= $nr->findOneBySlug($slug);  this method is same as that of the line before. doctrine is intelligent enough to access the property to find the one which we need 
        return $this->render('note/show.html.twig', [
            'note' => $note,
        ]);
    }
    #[IsGranted('IS_AUTHENTICATED_FULLY')]

    #[Route('/u/{username}', name: 'app_note_user', methods: ['GET'])]
    public function userNotes(
        string $username,
        UserRepository $user
    ): Response {
        $creators = $user->findByusername($username);
        $creator = $creators[0];
        return $this->render('note/user.html.twig', [
            'userNotes' => $creator->getNotes(),
            'creator' => $creator
        ]);
    }
    // #[IsGranted('IS_AUTHENTICATED_FULLY')]

    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {

        if (!$this->getUser()) {
            $this->addFlash('error', 'you need to be logged in');
            return $this->redirectToRoute('app_login');
        }


        $form = $this->createForm(NoteType::class); // Chargement du formulaire
        $form = $form->handleRequest($request); // Recuperation des données de la requête POST

        // Traitement des données
        if ($form->isSubmitted() && $form->isValid()) {
            $note = new Note();
            $note
                ->setTitle($form->get('title')->getData())
                ->setSlug($slugger->slug($note->getTitle()))
                ->setContent($form->get('content')->getData())
                ->setPublic($form->get('is_public')->getData())
                ->setCategory($form->get('category')->getData())
                ->setCreator($form->get('creator')->getData())
            ;
            $em->persist($note);
            $em->flush();
            $this->addFlash('success', 'Your note has been created successfully'); // added a flash with this method but this needs to be shown to the user inside the twig file  also . 
            return $this->redirectToRoute('app_note_show', ['slug' => $note->getSlug()]);
        }
        return $this->render('note/new.html.twig', [
            'noteForm' => $form
        ]);
    }
    #[IsGranted('IS_AUTHENTICATED_FULLY')]

    #[Route('/edit/{slug}', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, NoteRepository $nr, Request $request, EntityManagerInterface $em): Response
    {
        $note = $nr->findOneBySlug($slug);

        if ($this->getUser() != $note->getCreator()) {
            $this->addFlash('error', 'You can only edit your own note');
            return $this->redirectToRoute('app_note_show', ['slug' => $slug]);
        }

        $form = $this->createForm(NoteType::class, $note);
        $form = $form->handleRequest($request); // Recuperation des données de la requête POST

        // Traitement des données
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($note);
            $em->flush();
            $this->addFlash('success', 'Your note has been updated'); // added a flash with this method but this needs to be shown to the user inside the twig file  also . 
            return $this->redirectToRoute('app_note_show', ['slug' => $slug]);
        }
        return $this->render('note/edit.html.twig', [
            'noteForm' => $form,
        ]);
    }
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/delete/{slug}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug);
        $this->addFlash('success', 'The selected note has been deleted');
        return $this->redirectToRoute('app_note_user');
    }
}

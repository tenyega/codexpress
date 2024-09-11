<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


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

    #[Route('/{slug}', name: 'app_note_show')]
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

    #[Route('/{username}', name: 'app_note_user', methods: ['GET'])]
    public function userNotes(
        string $username,
        UserRepository $user
    ): Response {
        $creator = $user->findByusername($username);
        return $this->render('note/user.html.twig', [
            'userNotes' => $creator->getNotes(),
            'creator' => $creator
        ]);
    }
    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(): Response
    {
        return $this->render('note/new.html.twig');
    }

    #[Route('/edit/{slug}', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug);
        return $this->render('note/edit.html.twig', [
            'note' => $note
        ]);
    }

    #[Route('/delete/{slug}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug);
        $this->addFlash('success', 'The selected note has been deleted');
        return $this->redirectToRoute('app_note_user');
    }
}

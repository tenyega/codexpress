<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NoteController extends AbstractController
{

    /**
     * Shows all the notes available in the database
     */
    #[Route('/notes', name: 'notes')]
    public function all(NoteRepository $notes): Response
    {

        return $this->render('note/all.html.twig', [
            'notes' => $notes->findAll()
        ]);
    }

    /**
     * Shows one particular note based on its slug provided
     */
    #[Route('/note/{slug}', name: 'note_slug')]
    public function show(NoteRepository $notes, string $slug): Response
    {
        //Here the find method is used to find that one particular value based don the slug provided as a parameter with string format which is passed through url 
        return $this->render('note/show.html.twig', [
            'note' => $notes->findBy(['slug' => $slug])
        ]);
    }

    #[Route('/note', name: 'note')]
    public function note(): Response
    {

        return $this->render('note/note.html.twig');
    }

    #[Route('/note-edit', name: 'note_edit')]
    public function edit(): Response
    {

        return $this->render('note/edit.html.twig');
    }
}

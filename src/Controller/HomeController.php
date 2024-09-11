<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(NoteRepository $nr): Response
    {
        /**
         * Here we are getting the notes which are public in descending order which will show the recent ones first and only 6 notes 
         */
        $lastNotes = $nr->findBy(
            ['is_public' => true],
            ['created_at' => 'DESC'],
            6
        );
        return $this->render('home/index.html.twig', [
            'lastNotes' => $lastNotes
        ]);
    }

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(NoteRepository $nr): Response
    {

        return $this->render('home/login.html.twig', [
            'login' => 'LOGIN CONTROLLER'
        ]);
    }
}

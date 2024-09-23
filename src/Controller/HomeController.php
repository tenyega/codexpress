<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use App\Service\EmailNotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
            'lastNotes' => $lastNotes,
            'totalNotes' => count($nr->findAll())
        ]);
    }

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(NoteRepository $nr): Response
    {

        return $this->render('home/login.html.twig', [
            'login' => 'LOGIN CONTROLLER'
        ]);
    }


    #[Route('/email', name: 'app_email', methods: ['GET', 'POST'])]
    public function email(EmailNotificationService $ens, Request $request): Response
    {
        $case = $request->query->get('case');

        if ($case) {
            $ens->sendEmail($this->getUser()->getEmail(), $case);
        }
        return new Response("
            Email sent to {$this->getUser()->getEmail()} <br>
            Choose a case: <br>
            <a href='/email?case=premium'>Premium</a> <br>
            <a href='/email?case=registration'>Registration</a>
        ");
    }
}

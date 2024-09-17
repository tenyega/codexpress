<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CreatorController extends AbstractController
{

    #[Route('/users', name: 'app_users', methods: ['GET'])]
    public function users(UserRepository $ur): Response
    {
        return $this->render('creator/users.html.twig', [
            'users' => $ur->findAll()
        ]);
    }
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function profile(): Response
    {
        return $this->render('creator/profile.html.twig');
    }
    #[Route('/profile/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(): Response
    {
        $user = $this->getUser();
        return $this->render('creator/edit.html.twig', [
            'user' => $user,
        ]);
    }
}

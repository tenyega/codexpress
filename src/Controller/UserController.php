<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {

        return $this->render('user/profile.html.twig');
    }
    #[Route('/notifications', name: 'notifications')]
    public function notifications(): Response
    {

        return $this->render('user/notifications.html.twig');
    }
}

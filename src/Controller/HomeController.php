<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        return $this->render('home/index.html.twig');
    }
   


    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {

        return $this->render('home/profile.html.twig');
    }

    #[Route('/category', name: 'category')]
    public function category(): Response
    {

        return $this->render('home/category.html.twig');
    }
}

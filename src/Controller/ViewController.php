<?php

namespace App\Controller;

use App\Entity\View;
use App\Repository\LikeRepository;
use App\Repository\NoteRepository;
use App\Repository\ViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ViewController extends AbstractController
{
    #[Route('/view', name: 'app_view', methods: ['POST'])]
    public function add(Request $request, ViewRepository $vr, NoteRepository $nr, EntityManagerInterface $em): Response
    {
        $referer = $request->headers->get('referer');
        $view = $vr->findBynote($request->request->get('note_id_view'), $this->getUser());
        $note = $nr->findOneById($request->request->get('note_id_view'));


        $newView = new View();
        $newView
            ->setNote($note)
            ->setIpAddress($request->getClientIp());
        $em->persist($newView);
        $em->flush();


        return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_note_all');
    }
}

<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Note;
use App\Entity\User;
use App\Entity\View;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use App\Repository\ViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/notes')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_all', methods: ['GET'])]
    public function all(NoteRepository $nr, Request $request,  PaginatorInterface $paginator,): Response
    {
        /**
         * Get the collection of all the notes that are public and in descending order which is the most recently create first 
         */
        $all_notes = $nr->findBy(['is_public' => true], ['created_at' => 'DESC']);
        $pagination = $paginator->paginate(
            $all_notes, /* query NOT result in the form of an array or persistant collection and collection array  */
            $request->query->getInt('page', 1), /*page number, looks for a page variable inside the url*/
            10 /*limit per page*/
        );
        return $this->render('note/all.html.twig', [
            'all_notes' => $pagination,
        ]);
    }
    #[IsGranted('IS_AUTHENTICATED_FULLY')]

    #[Route('/n/{slug}', name: 'app_note_show')]
    public function show(NoteRepository $nr, UserRepository $ur, Request $request, string $slug, EntityManagerInterface $em, RequestStack $requestStack, ViewRepository $vr): Response
    {

        /**
         * Get the collection of all the notes that are public and in descending order which is the most recently create first 
         */
        $note = $nr->findOneBy(['slug' => $slug]);
        // $array = $note->getCreator()->getNotes()->toArray();
        // $creatorNotes = array_slice($array, 0, 3);
        // need to add the view to the view table where note_id= $note->getId()
        $view = new View();
        $view->setNote($note)
            ->setIpAddress($requestStack->getCurrentRequest()->getClientIp());
        $em->persist($view);
        $em->flush();
        $creatorNotes = $nr->findByCreator($note->getCreator()->getId()) ?? [];
        $views = $vr->findBynote($note->getId());

        if ($request->get('note_id')) {
            $note_id = $request->get('note_id'); //3243    
            $creator_id = $request->get('creator_id'); //3243    
            // when the user have clicked on like button 
            $like = new Like();
            $like->setNote($nr->findOneBy(['id' => $note_id]))
                ->setCreator($ur->findOneBy(['id' => $creator_id]));

            $em->persist($like);
            $em->flush();
        }
        // $note= $nr->findOneBySlug($slug);  this method is same as that of the line before. doctrine is intelligent enough to access the property to find the one which we need 
        return $this->render('note/show.html.twig', [
            'note' => $note,
            'creatorNotes' => $creatorNotes,
            'views' => $views
        ]);
    }
    #[IsGranted('IS_AUTHENTICATED_FULLY')]

    #[Route('/u/{username}', name: 'app_note_user', methods: ['GET', 'POST'])]
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
                ->setCreator($this->getUser())
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

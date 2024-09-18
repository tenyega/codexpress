<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET', 'POST'])]
    public function search(Request $request, NoteRepository $nr, PaginatorInterface $paginator): Response
    {

        /** 
         *if ($request->isMethod('POST')) {
         *   dd($request->get('q')); // this is the case when we click on the search button in our home page. 
         *} else {
         *    dd('je ne suis pas en post'); // this is the case when we write directly in the url of the navigator localhost/search 
         *}
         */
        $searchQuery = $request->get('q');

        if (!$searchQuery) {
            return $this->render('search/results.html.twig');
        }


        $pagination = $paginator->paginate(
            $nr->findByQuery($searchQuery),
            $request->query->getInt('page', 1),
            24
        );
        // dd($pagination);
        return $this->render('search/results.html.twig', [
            'all_notes' => $pagination,
            'searchQuery' => $searchQuery,
        ]);
    }
}

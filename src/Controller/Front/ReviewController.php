<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReviewController extends AbstractController
{
    /**
     * Ajout d'une critique pour un film donné
     * 
     * @Route("/review/add/movie/{id<\d+>}", name="app_review_add", methods={"GET", "POST"})
     */
    public function add(Movie $movie, Request $request, ReviewRepository $reviewRepository): Response
    {

        // grâce au ParamConverter, on a récupéré notre film $movie
        // qui est de type App\Entity\Movie, dont l'{id} est celui de l'URL
        // @see https://symfony.com/doc/5.4/doctrine.html#automatically-fetching-objects-paramconverter
        
        $review = new Review();

        if (!$this->isGranted('time', $review)) {
            $this->addFlash('danger', 'Trop tard!');
        }

        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on associe la review au film courant
            $review->setMovie($movie);

            // on sauve en BDD
            $reviewRepository->add($review, true);

            // on redirige vers la page du film concerné
            return $this->redirectToRoute('main_movie_show', ['slug' => $movie->getSlug()]);
        }

        return $this->renderForm('front/review/add.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }
}

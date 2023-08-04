<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Service\MySlugger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Contrôleur qui gère les films et séries
 * Un préfixe est précisé ici
 * 
 * @Route("/back/movie")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/", name="app_back_movie_index", methods={"GET"})
     */
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('back/movie/index.html.twig', [
            'movies' => $movieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_back_movie_new", methods={"GET", "POST"})
     */
    public function new(
        Request $request,
        MovieRepository $movieRepository,
        MySlugger $mySlugger
    ): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $movie->setSlug($mySlugger->slugify($movie->getTitle()));
            
            $movieRepository->add($movie, true);

            $this->addFlash('success', 'Film ou série ajouté.e');

            return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="app_back_movie_show", methods={"GET"})
     */
    public function show(Movie $movie = null): Response
    {
        // en mettant $movie = null, on récupère la main sur la 404 du ParamConverter

        // 404 ?
        if ($movie === null) {
            throw $this->createNotFoundException('Film ou série non trouvé.e');
        }

        return $this->render('back/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{id<\d+>}/edit", name="app_back_movie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Movie $movie, MovieRepository $movieRepository): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $movieRepository->add($movie, true);

            // flash message
            // label, message
            // astuce : on utilise comme label un style Bootstrap pour rendre dynamique l'affichage de l'alerte
            $this->addFlash('success', 'Film ou série modifié.e');
            // un deuxième message, si besoin
            $this->addFlash('danger', 'Attention derrière toi !');

            return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="app_back_movie_delete", methods={"POST"})
     */
    public function delete(Request $request, Movie $movie, MovieRepository $movieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {

            $movieRepository->remove($movie, true);

            $this->addFlash('success', 'Le film ou la série '.$movie->getTitle().' a été supprimé.e');
        }

        return $this->redirectToRoute('app_back_movie_index', [], Response::HTTP_SEE_OTHER);
    }
}

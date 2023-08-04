<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/back/season")
 */
class SeasonController extends AbstractController
{
    /**
     * @Route("/movie/{id}", name="app_back_season_index", methods={"GET"})
     */
    public function index(SeasonRepository $seasonRepository, Movie $movie): Response
    {
        return $this->render('back/season/index.html.twig', [
            // va chercher les objets saisons dont la propriété est la série voulue
            // grâce à findBy (condition WHERE SQL)
            'seasons' => $seasonRepository->findBy(['movie' => $movie]),
            // la série associée
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/new/movie/{id}", name="app_back_season_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SeasonRepository $seasonRepository, Movie $movie): Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on associe le film à la saison (car plus dans le formulaire)
            $season->setMovie($movie);

            $seasonRepository->add($season, true);

            return $this->redirectToRoute('app_back_season_index', ['id' => $movie->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/season/new.html.twig', [
            'season' => $season,
            'form' => $form,
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_back_season_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Season $season, SeasonRepository $seasonRepository): Response
    {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seasonRepository->add($season, true);

            return $this->redirectToRoute('app_back_season_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/season/edit.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_back_season_delete", methods={"POST"})
     */
    public function delete(Request $request, Season $season, SeasonRepository $seasonRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->request->get('_token'))) {
            $seasonRepository->remove($season, true);
        }

        return $this->redirectToRoute('app_back_season_index', ['id' => $season->getMovie()->getId()], Response::HTTP_SEE_OTHER);
    }
}

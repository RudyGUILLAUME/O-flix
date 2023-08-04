<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Model\Movies;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    /**
     * API de récupération de l'ensemble des genres
     * Method = GET, pas de paramètre
     * 
     * @Route("/api/genres", name="api_genres_collection", methods={"GET"})
     */
    public function index(GenreRepository $gr): Response
    {
        $genreList = $gr->findAll();

        return $this->json(
            // La liste des genres à sérialiser
            $genreList,
            // Code de retour HTTP
            200,
            // Tableau des headers complémentaires à envoyer 
            // Avec la réponse
            [],
            // Groupes a envoyer avec la réponse
            ['groups' => 'get_genres_collection']
        );
    }

    /**
     * API de récupération de l'ensemble des films
     * Method = GET, pas de paramètre
     * 
     * @Route("/api/genres/{id}/movies", name="api_genres_get", methods={"GET"})
     */
    public function getGenreAndMovies(Genre $genre): Response
    {
        $moviesList = $genre->getMovies();

        $returnResult = [
            'genre' => $genre,
            'movies' => $moviesList
        ];
        return $this->json(
            // La liste des genres à sérialiser
            $returnResult,
            // Code de retour HTTP
            200,
            // Tableau des headers complémentaires à envoyer 
            // Avec la réponse
            [],
            // Groupes a envoyer avec la réponse
            ['groups' => 'get_collection']
        );
    }
}

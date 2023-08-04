<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use App\Model\Movies;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MovieController extends AbstractController
{
    /**
     * API de récupération de l'ensemble des films
     * Method = GET, pas de paramètre
     * 
     * @Route("/api/movies", name="api_movies_get", methods={"GET"})
     */
    public function index(MovieRepository $mr): Response
    {
        $moviesList = $mr->findAll();

        return $this->json(
            // La liste des films à sérialiser
            $moviesList,
            // Code de retour HTTP
            200,
            // Tableau des headers complémentaires à envoyer 
            // Avec la réponse
            [],
            // Groupes a envoyer avec la réponse
            ['groups' => 'get_collection']
        );
    }

    /**
     * API de récupération de l'ensemble des films d'un genre donné
     * Method = GET, paramètre {slug}
     * 
     * @Route("/api/movies/{id<\d+>}", name="api_movies_get_item", methods={"GET"})
     */
    public function movie(Movie $movie = null, MovieRepository $mr): Response
    {
        if ($movie === null) {
            return $this->json([
                'error' => true,
                'message' => "Le film demandé n'éxiste pas"
            ], 404, [], []);
        }
        return $this->json(
            // La liste des films à sérialiser
            $movie,
            // Code de retour HTTP
            200,
            // Tableau des headers complémentaires à envoyer 
            // Avec la réponse
            [],
            // Groupes a envoyer avec la réponse
            ['groups' => 'get_item']
        );
    }

    /**
     * API de récupération d'un film au hasard
     * Method = GET, pas de paramètre
     * 
     * @Route("/api/movies/random", name="api_movies_random", methods={"GET"})
     */
    public function randomMovie(MovieRepository $mr): Response
    {

        // On utilise le repository pour aller chercher un
        // film random
        $randomMovie = $mr->getOneRandomMovie();

        return $this->json(
            // La liste des films à sérialiser
            $randomMovie,
            // Code de retour HTTP
            200,
            // Tableau des headers complémentaires à envoyer 
            // Avec la réponse
            [],
            // Groupes a envoyer avec la réponse
            ['groups' => 'get_item']
        );
    }

    /**
     * API de création d'un film dont les données ont été fournies en 'POST'
     * Method = POST, données post encodées json dans le corps de la Requette
     * 
     * @Route("/api/movies", name="api_movies_post", methods={"POST"})
     */
    public function createMovie(
        Request $request,
        MovieRepository $mr,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response
    {

        $jsonContent = $request->getContent();

        $movie = $serializer->deserialize($jsonContent, Movie::class, 'json');

        // Valider l'entité avec notre validation
        // vérifiera que les contraintes formulées dans l'entité
        // sont respectées (NotBlank, ...)
        $errors = $validator->validate($movie);

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Méthode add et remove du repository
        // sont créées lors du make:entity
        $mr->add($movie, true);

        return $this->json(
            // La liste des films à sérialiser
            $movie,
            // Code de retour HTTP
            Response::HTTP_CREATED,
            // Tableau des headers complémentaires à envoyer 
            // Avec la réponse
            [
                // Nom de l'en-tête + URL
                'Location' => $this->generateUrl('api_movies_get_item', ['id' => $movie->getId()])
            ],
            // Groupes a envoyer avec la réponse
            ['groups' => 'get_item']
        );
    }
}

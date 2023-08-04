<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Entity\Season;
use App\Model\Movies;
use App\Repository\CastingRepository;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use App\Service\MessageGenerator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController {

       
    /**
     * Controller de la page d'accueil
     *
     * @Route("/", name="main_home")
     * 
     * @return Response
     */
    public function home(
        MovieRepository $mr,
        GenreRepository $gr
    ): Response
    {
        //dump($mg->getHappyMessage());  // On utilise notre service
        // Ajout d'un flash message en session
        //$this->addFlash('Success', 'Bienvenue sur oFlix');

        // On cherche les données de films pour pouvoir les afficher
        // En dynamique dans la page
        $movies = $mr->findLatestByReleaseDateDQL();
 
        return $this->render("front/main/home.html.twig", [
            'movies' => $movies,
            'genres' => $gr->findAll()
        ]);
    }

    /**
     * Controller pour l'affichage d'un film
     *
     * @Route("/movie/{slug}", name="main_movie_show")
     * 
     * @return Response
     */
    public function movieShow(Movie $movie = null, CastingRepository $cr): Response
    {
        // Attention il n'y a pas de contrôle sur l'id passé
        // Il faudra le faire dans le controlleur
        
        // On recherche les données pour 1 film
        // Instanciation de Movies
        
        // 404 ?
        if ($movie === null) {

            // Flash message
            // $this->addFlash("Erreur", "le film demandé n'existe pas");

            // Ou

            // Exception
            throw $this->createNotFoundException('Film/série non trouvé(e)');

            // Ou

            // Redirection vers une autre page
            // return $this->redirect("http://perdu.com"); // Redirection externe
            // return $this->redirectToRoute('main_home'); // Redirection vers route 
        }

        $castings = $cr->findAllCastingsByMovieDQL($movie);

        return $this->render("front/main/movie-show.html.twig", [
            // On initialise la variable twig 'movie' avec les données du modéle
            'movie' => $movie,
            'castings' => $castings
        ]);
    }

    /**
     * Controlleur de l'affichage de tous les films et séries
     *
     * @Route("/movies/list/{page<\d+>}", name="main_movies_list")
     * 
     * @return Response
     */
    public function list(
        Request $request,
        MovieRepository $mr,
        GenreRepository $gr,
        int $page = 1
    ): Response
    {
        $search = $request->query->get('search', '%');
        
        $search = '%' . $search . '%';
 
        $result = $mr->paginatedFind($search, $page, 5);

        return $this->render("front/main/movies-list.html.twig", [
            'movies' => $result['movies'],
            'pagination' => $result,
            'genres' => $gr->findAll()
        ]);
    }

    /**
     * Theme switcher
     * Toggles Netflix and Allociné themes
     * 
     * @Route("/theme/toggle", name="main_theme_switcher")
     */
    public function themeSwitcher(SessionInterface $session)
    {
        // Notre but est de stocker en session utilisateur
        // le theme choisi

        // On récupère le thème de la session
        $theme = $session->get('theme', 'netflix');

        // On "inverse" le thème
        if ($theme === 'netflix') {
            $session->set('theme', 'allocine');
        } else {
            $session->set('theme', 'netflix');
        }

        // On redirige vers la home
        return $this->redirectToRoute('main_home');

        // Puis dans le template base.html.twig
        // on conditionnera la CSS de la nav selon le thème choisi
    }
}
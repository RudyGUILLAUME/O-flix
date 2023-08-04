<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Service\FavoritesManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoritesController extends AbstractController
{
    /**
     * @Route("/favorites", name="favorites_list")
     */
    public function list(): Response
    {
        return $this->render('front/favorites/list.html.twig');
    }

    /**
     * Favorite gestion
     * 
     * @Route("/favorites/gestion/{id}", name="favorite_gestion", requirements={"id"="\d+"})
     */
    public function favorite(Movie $movie, FavoritesManager $favoritesManager)
    {
        // Ce code doit partir dans le service
        // A la place on doit utiliser les méthodes du service
        
        if ($favoritesManager->toggle($movie) === 'add') {
            $message = "ajouté(e) aux favoris";
        } else {
            $message = "retiré(e) des favoris";
        }
        $this->addFlash('success', "'" . $movie->getTitle() . "' " . $message);

        return $this->redirectToRoute('favorites_list');
    }

    /**
     * Suppression de toute la liste de favorites
     * 
     * @Route("/favorites/unset", name="favorite_unset")
     */
    public function unsetFav(FavoritesManager $favoritesManager)
    {
        // Ce code doit partir dans le service
        // A la place on doit utiliser les méthodes du service
        
        if ($favoritesManager->empty() === true) {
            $this->addFlash('success', "Votre liste de favoris à bien été vidée");
            return $this->redirectToRoute('main_home');
        } else {
            $this->addFlash('danger', "Cette action est interdite");
            return $this->redirectToRoute('favorites_list');
        }

    }
}

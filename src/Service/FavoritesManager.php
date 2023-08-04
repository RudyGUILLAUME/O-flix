<?php

namespace App\Service;

use App\Entity\Movie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FavoritesManager {

    private $session;

    // Configuration en dur de la fonctionnalité
    private $emptyEnabled;

    // Si je dois passer par des injections de dépendances
    // Alors je dois le faire dans un constructeur
    public function __construct(SessionInterface $session, bool $emptyEnabled)
    {
        $this->session = $session;
        $this->emptyEnabled = $emptyEnabled;  
    }

    public function toggle(Movie $movie)
    {
        // permet d'ajouter ou supprimer le film en session suvant qu'il 
        // y est déja ou pas

        // Récuperer les favoris qui sont en session
        $myFavorites = $this->session->get('myFavorites', []);

        $id = $movie->getId();

        if (array_key_exists($id, $myFavorites)) {
            // Le unset permet de supprimer la clé du tableau
            unset($myFavorites[$id]);

            $action = 'remove';

        } else {
            $myFavorites[$id] = $movie;

            $action = 'add';
        }

        // Je stocque dans la session à l'index 'myFavorites', mon tableau
        $this->session->set('myFavorites', $myFavorites);

        return $action;
    }

    public function empty()
    {
        // Permet de vider l'ensemble des favoris

        if ($this->emptyEnabled) {
            $this->session->remove('myFavorites');
        }
        return $this->emptyEnabled;
    }
}
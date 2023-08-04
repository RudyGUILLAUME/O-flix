<?php

namespace App\EventSubscriber;

use App\Repository\MovieRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment as Twig;

class RandomMovieSubscriber implements EventSubscriberInterface
{

    private $mr;        // MovieRepository
    private $twig;      // Twig Environment

    /**
     * Constructeur pour récuperer par injection de dépendance
     * Le repository Movie
     *
     * @param MovieRepository $mr
     */
    public function __construct(MovieRepository $mr, Twig $twig)
    {
        $this->mr = $mr;  
        $this->twig = $twig;
    }

    /**
     * Methode qui est appelée lorsque l'event kernel.controller
     * est lancé.
     *
     * @param ControllerEvent $event
     * @return void
     */
    public function onKernelController(ControllerEvent $event)
    {
        // Je regarde dans quel controlleur on est afin de limiter le 
        // positionnement de la variable twig aux routes faisant partie 
        // du front 

        $controller = $event->getController();
        // Lorsque la route a donné un errreur, une exception
        //Le controlleur n'est pas un tableau
        if (is_array($controller)) {
            // Je récupère l'index 0 du tableau qui contient l'objet controlleur
            // A l'index 1 du tableau on a la route
            $controller = $controller[0];
        }
        
        // On récupere le nom de la classe de notre objet controller
        $controllerClass = get_class($controller);

        if (strpos($controllerClass, 'App\Controller\Front') === false) {
            return;
        }

        // Utilisation du repository pour chercher un film
        // au hasard
        $film = $this->mr->getOneRandomMovie();

        // Ajout dans Twig Environment d'une variable globale
        // Contenant le film 
        $this->twig->addGlobal('randomMovie', $film);

    }

    /**
     * Cette fonction permet de préciser les événements
     * que l'on souhaite "écouter", on précise également pour evt écouté, la
     * méthode qui doit être appelée
     *
     * @return void
     */
    public static function getSubscribedEvents()
    {
        return [
            // Types d'evt que je souhaite gérer
            'kernel.controller' => 'onKernelController'
        ];
    }
}
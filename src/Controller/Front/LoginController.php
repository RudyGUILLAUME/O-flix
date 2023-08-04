<?php

namespace App\Controller\Front;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * Affiche le formulaire de login (GET 'login_path')
     * 
     * La partir POST => 'check_path' est gérée par symfony
     * 
     * @Route("/login", name="login_index")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Les deux lignes suivantes servent en cas d'échec à la connexion
        // C'est à dire si on réaffiche le formulaire suite a erreur

        // Récupère la dernière erreur
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier 'username' saisi par l'utilisateur
        // En l'occurence, pour oFlix ce sera 'email'
        $lastUsername = $authenticationUtils->getLastUsername();

        // Affichage du formulaire de login avec erreur et dernier user saisi
        // si c'est la cas
        return $this->render('front/login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * Logout
     * 
     * @Route("/logout", name="login_logout")
     */
    public function logout()
    {
        // Ce code ne sera jamais exécuté
        // le composant de sécurité va intercepter la requête avant.
    }
}
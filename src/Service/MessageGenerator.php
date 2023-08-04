<?php

// src/Service/MessageGenerator.php
namespace App\Service;

use Symfony\Component\Security\Core\Security;

class MessageGenerator
{
    private $user ;
    private $nomParDefaut;

    // Dans le constructeur du service, je peux utiliser 
    // L'injection de dépendances, comme par exemple récupérer
    // Le User courant pour personnaliser le message
    public function __construct(Security $security, string $nomParDefaut)
    {
        $this->nomParDefaut = $nomParDefaut;
        $this->user = $security->getUser();
    }

    public function getHappyMessage(): string
    {
        $messages = [
            'You did it! You updated the system! Amazing!',
            'That was one of the coolest updates I\'ve seen all day!',
            'Great work! Keep going!',
        ];

        $index = array_rand($messages);

        if ($this->user === null)
            $message = $this->nomParDefaut;
        else
            $message = $this->user->getUserIdentifier();

        return "Bonjour $message, " . $messages[$index];
    }
}
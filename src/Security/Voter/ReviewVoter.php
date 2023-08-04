<?php

namespace App\Security\Voter;

use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

    /**
     * Antoine : vous prenez un endroit avec pleins de guichet, tu as un
     * pass qui ouvre un guichet spécifique, tu vas au premier il te dis
     * "ah non c'est pas moi qui gère ca tu vas au second etc etc jusqu'a
     * ce qu'un te dise ah oui ce pass la c'est moi qui gère" (methode supports)
     * 
     * Une fois que le guichet est identifié, on regarde avec lui les conditions
     * (méthode voteOnAttributes)
     * Les voters sont appelés par Symfony a chaque fois qu'une décision
     * d'acces doit être prise
     * 
     * Le vote s'effectue en deux étapes
     * 
     * 1/ - appel de la fonction 'supports' qui doit renvoyer 'true' si elle
     * sait gérer la chaine de caractères $attribute avec l'objet passé dans
     * $subject. Elle renvoie false sinon (ce qui revient a s'abstenir de voter)
     * 
     * 2/ - Si 'supports' a renvoyé 'true' alors la fonction 'voteOnAttribute' 
     * est appelée pour la prise de décision. Cette méthode renvoie true si l'accès 
     * est autorisé, false sinon
     * 
     * La chaine de tous les voters symfony est mise en oeuvre et les voters
     * sont appelés dans l'ordre de leur définition jusqu'à ce que l'un d'eux
     * réponde true (GRANTED), si tous les voters ont répondu false (DENIED)
     * ou se sont abstenus (ABSTAIN) alors l'accès est refusé
     *
     * 
     * @param string $attribute
     * @param [type] $subject
     * @return boolean
     */
class ReviewVoter extends Voter
{
    public const TIME = 'time'; // Vérifie que l'heure limite n'est pas dépassée
    public const USER = 'user'; // Vérifie que le user est ok ()

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::TIME, self::USER])
            && $subject instanceof \App\Entity\Review;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {

            // Je vérifie que l'utilisateur est connecté
            case self::USER:
                $user = $token->getUser();
                // if the user is anonymous, do not grant access
                if (!$user instanceof UserInterface) {
                    return false;
                } else {
                    return true;
                }
                break;

            case self::TIME:
                $now = new DateTime();
                $limitTime = new DateTime();
                $limitTime->setTime(14,30);

                // Pour éviter de bloquer les new review après 14h30
                return true;

                //if ($now >= $limitTime) {
                //    return false;
                //} else {
                //    return true;
                //}
                break;
        }
        return false;
    }
}

<?php

namespace App\Tests\Controller\Front;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FavoritesControllerTest extends WebTestCase
{
    /**
     * ROLE_USER Add favorites
     */
    public function testRoleUserFavoriteAdd(): void
    {
        // On crée un client
        $client = static::createClient();

        // Le Repo des Users
        $userRepository = static::getContainer()->get(UserRepository::class);
        // On récupère user@user.com
        $testUser = $userRepository->findOneByEmail('user@user.com');
        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // On exécute une requête HTTP en GET sur l'URL /film-1
        $crawler = $client->request('GET', '/favorites/gestion/2');

        // Status code 302 (redirection)
        $location = $client->getResponse()->headers->get('Location');

        $this->assertSame('/favorites', $location);
        $this->assertResponseStatusCodeSame(302);
    }
}
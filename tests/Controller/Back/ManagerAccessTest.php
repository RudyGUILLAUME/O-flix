<?php

namespace App\Tests\Controller\Back;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ManagerAccessTest extends WebTestCase
{
    /**
     * Routes en GET pour Anonymous
     * 
     * @dataProvider getUrlsAuthorized
     */
    public function testPageGetIsRedirectAuthorized($url)
    {
        $client = self::createClient();

        // Le Repo des Users
        $userRepository = static::getContainer()->get(UserRepository::class);
        // On récupère user@user.com
        $testUser = $userRepository->findOneByEmail('manager@manager.com');
        // simulate $testUser being logged in
        $client->loginUser($testUser);
        
        $client->request('GET', $url);
        
        $this->assertResponseStatusCodeSame(200);
    }    
    
    public function getUrlsAuthorized()
    {
        yield ['/back/movie/'];   // 200
        yield ['/back/movie/1'];  // 200
        yield ['/back/user/'];    // 200
        yield ['/back/user/1'];   // 200
    }

    /**
     * Routes en GET pour Anonymous
     * 
     * @dataProvider getUrlsDenied
     */ 
    public function testPageGetIsRedirectDenied($url)
    {
        $client = self::createClient();

        // Le Repo des Users
        $userRepository = static::getContainer()->get(UserRepository::class);
        // On récupère user@user.com
        $testUser = $userRepository->findOneByEmail('manager@manager.com');
        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame(403);
    }    

    public function getUrlsDenied()
    {
        yield ['/back/casting/1/edit']; // 403
        yield ['/back/movie/new'];      // 403
        yield ['/back/movie/1/edit'];   // 403
        yield ['/back/user/new'];       // 403
        yield ['/back/user/1/edit'];    // 403
    }

    /**
     * Idem en POST
     */
}
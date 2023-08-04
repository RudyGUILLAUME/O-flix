<?php

namespace App\Tests\Controller\Back;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminAccessTest extends WebTestCase
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
        $testUser = $userRepository->findOneByEmail('admin@admin.com');
        // simulate $testUser being logged in
        $client->loginUser($testUser);
        
        $client->request('GET', $url);
        
        $this->assertResponseStatusCodeSame(200);
    }    
    
    public function getUrlsAuthorized()
    {
        yield ['/back/movie/'];         // 200
        yield ['/back/movie/1'];        // 200
        yield ['/back/user/'];          // 200
        yield ['/back/user/1'];         // 200
        yield ['/back/casting/1/edit']; // 200
        yield ['/back/movie/new'];      // 200
        yield ['/back/movie/1/edit'];   // 200
        yield ['/back/user/new'];       // 200
        yield ['/back/user/1/edit'];    // 200
    }


    /**
     * Idem en POST
     */
}
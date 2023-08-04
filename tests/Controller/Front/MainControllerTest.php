<?php

namespace App\Tests\Controller\Front;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    /**
     * Home
     */
    public function testHome(): void
    {
        // On crée un client
        $client = static::createClient();
        // On exécute une requête HTTP en GET sur l'URL /
        $crawler = $client->request('GET', '/');

        // A-t-on un status code entre 200 et 299
        $this->assertResponseIsSuccessful();
        // Ou status code 200
        // $this->assertResponseStatusCodeSame(200);
        
        // Est-on sur la home ?
        $this->assertSelectorTextContains('h1', 'Films, séries TV et popcorn en illimité.');

        $this->assertPageTitleContains("Bienvenue sur O'flix");
    }
/**
     * Home
     */
    public function testRandomFilm(): void
    {
        // On crée un client
        $client = static::createClient();
        // On exécute une requête HTTP en GET sur l'URL /
        $crawler = $client->request('GET', '/');

        // A-t-on un status code entre 200 et 299
        $this->assertResponseIsSuccessful();
        // Ou status code 200
        // $this->assertResponseStatusCodeSame(200);
        
        // Est-on sur la home ?

        $this->assertSelectorTextContains('p[data-test="test-random"]', "Un film à voir : Film ");
    }

    /**
     * Movie show
     */
    public function testMovieShow(): void
    {
        // On crée un client
        $client = static::createClient();
        // On exécute une requête HTTP en GET sur l'URL /film-1
        $crawler = $client->request('GET', '/movie/film-1');

        // Status code 200
        $this->assertResponseStatusCodeSame(200);

        // Est-on sur la home ?
        $this->assertSelectorTextContains('h3', 'Film 1');
    }

    /**
     * Anonymous Add Review
     */
    public function testAnonymousReviewAdd(): void
    {
        // On crée un client
        $client = static::createClient();
        // On exécute une requête HTTP en GET sur l'URL /film-1
        $crawler = $client->request('GET', '/review/add/movie/1');

        // On doit avoir une redirection (status code 302)
        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * ROLE_USER Add Review
     */
    public function testRoleUserReviewAdd(): void
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
        $crawler = $client->request('GET', '/review/add/movie/1');

        // Status code 200 (OK !)
        $this->assertResponseStatusCodeSame(200);
        // Le texte du h1
        $this->assertSelectorTextContains('h2', 'Ajouter une critique');

        
    }

    /**
     * ROLE_USER Add Review form post
     */
    public function testRoleUserReviewAddPostWithoutErrors(): void
    {
        // On crée un client WebTestCase
        $client = static::createClient();

        // On récupère via le container de service le Repo des Users
        $userRepository = static::getContainer()->get(UserRepository::class);

        // On récupère user@user.com
        // findOneByEmail et findOneByUsername sont des méthodes apportées
        // par le bundle UserRepository (sécurité) de symfony 
        $testUser = $userRepository->findOneByEmail('user@user.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // On exécute une requête HTTP en GET sur l'URL /film-1
        $crawler = $client->request('POST', '/review/add/movie/1');

        // On recherche le bouton dans le <form>
        $buttonCrawlerNode = $crawler->selectButton('Ajouter');

        // On recherche le form associé au bouton
        $form = $buttonCrawlerNode->form();

        $form['review[username]'] = 'Yo';
        $form['review[email]'] = 'user@user.com';
        // Min 100 caractères
        $form['review[content]'] = 'Fd fdsf ds fds fds fds  fd dsf dsf ds fds fs fds fds fs fsfsf ds fds fdsfds fs fsf ds fdsf dsfsf dsfdsfdst ertierteriteroiteroiterotierotierotierotierotierotoetieroitoetioeitoeitoe';
        $form['review[rating]'] = '5';
        $form['review[reactions]'] = ["smile","cry"];
        // Le champ date en 3 parties (voir l'inspecteur chrome)
        $form['review[watchedAt][month]'] = '12';
        $form['review[watchedAt][day]'] = '12';
        $form['review[watchedAt][year]'] = '1952';

        // Soumission du formulaire
        $crawler = $client->submit($form);

        // On s'attend a une redirection vers une autre page
        // -> La liste des films
        // Check HTTP 302
        $this->assertResponseStatusCodeSame(302);
    }
    /**
     * ROLE_USER Add Review form post
     */
    public function testRoleUserReviewAddPostWithErrors(): void
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
        $crawler = $client->request('POST', '/review/add/movie/1');

        $buttonCrawlerNode = $crawler->selectButton('Ajouter');

        $form = $buttonCrawlerNode->form();

        $form['review[username]'] = 'Yo';
        $form['review[email]'] = 'user@user.com';
        // content vide ne respecte pas la contrainte min 100 caractères
        $form['review[content]'] = '';
        $form['review[rating]'] = '5';
        $form['review[reactions]'] = ["smile","cry"];
        
        $crawler = $client->submit($form);
        // On attend HTTP 422 - Unprocessable Content
        $this->assertResponseStatusCodeSame(422);
    }
}

<?php
/**
 * Pour réaliser les tests avec KartikJWT il faut initialiser le service
 * dans le mode 'test'
 * La doc : https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/3-functional-testing.html
 * Explique les étapes en détail, et sont résumées ci-dessous
 * 
 * Avant toutes choses, les API doivent être fonctionelles en mode 'dev'
 * Ne pas oublier de runner la commande `php bin/console lexik:jwt:generate-keypair`
 * 
 * 1/ - Création des clés ssl spécifiques pour le test (a éxecuter a la racine projet)
 * 
 *  openssl genrsa -out config/jwt/private-test.pem -aes256 4096
 *  openssl rsa -pubout -in config/jwt/private-test.pem -out config/jwt/public-test.pem
 * 
 * 2/ - Fichier de config test pour LexikJWT
 * 
 * Ajouter un fichier de config de test pour kartikJWT
 * (créer un ss rep 'test' dans config)
 * 
 * # config/test/lexik_jwt_authentication.yaml
 * lexik_jwt_authentication:
 *   secret_key: '%kernel.project_dir%/config/jwt/private-test.pem'
 *   public_key: '%kernel.project_dir%/config/jwt/public-test.pem'
 * 
 * 3/ - Et c'est tout bon
 * 
 */
namespace App\Tests\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{
    /**
     * Create a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createAuthenticatedClient(
        $username = '',
        $password = '')
    {
        // Création d'un client de test
        $client = static::createClient();

        // Requete en 'POST' sur l'api login_check pour obtenir le token
        // d'authentification
        // Le username et password sont encodés JSON et transmis dans le 
        // corps de la requete
        $client->request(
        'POST',
        '/api/login_check',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            'username' => $username,
            'password' => $password,
        ])
        );

        // On récupère dans la réponse un tableau avec le token sous la forme
        // data['token'] = '<token>'
        $data = json_decode($client->getResponse()->getContent(), true);

        // Utilisation du token pour créer le header Authorization: 
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    /**
     * Routes en GET pour Anonymous
     * 
     */
    public function testApiConnected()
    {
        // Le but de cette fonction est de réaliser l'authentification
        // de l'utilisateur
        $client = $this->createAuthenticatedClient("user@user.com", "user");

        // On récupère ici un client qui à déjà un token valide dans les headers
        // sous la forme
        // Authorization: Bearer <token>
        $client->request(
            'GET',
            '/api/movies',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );

        // Décodage de la chaine de caractères JSON en tableau d'objets
        $response = json_decode($client->getResponse()->getContent());

        // Vérification que l'on a bien 20 films en retour de l'API
        $this->assertEquals(count($response), 20);
        // Et que le titre du 1er est bien 'Film 1'
        // Ici ce sont les fixtures de tests qui sont utilisées
        $this->assertEquals($response[0]->title, 'Film 1');
    }

    /**
     * Routes en GET pour Anonymous
     * 
     */
    public function testApiNotConnected()
    {
        $client = self::createClient();

        $client->request(
            'GET',
            '/api/movies',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
            );
        $response = json_decode($client->getResponse()->getContent());

        $this->assertEquals($response->code, "401");
        $this->assertEquals($response->message, "JWT Token not found");
    }
}
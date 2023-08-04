# Commandes pour les tests avec phpunit

Il faut créér un fichier .env.test.local qui reprend les 
mêmes informations que le .env

Il faut créer la database de tests

```bash
php bin/console doctrine:database:create --env=test
```

Il faut ensuite executer les migrations

```bash
php bin/console doctrine:migration:migrate --env=test
```

On charge les données de tests (dans AppFixuresTest.php)

```bash
php bin/console doctrine:fixture:load --env=test
```

On ajoute les slug manquants dans la table movie

```bash
php bin/console oflix:movies:slugify --env=test
```

Ensuite les tests sont dans le répèrtoire 'tests'

Les classes héritent de TestCase ou WebTestCase

Les méthodes implémentant des tests doivent commencer par 'test'

```php

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
```

Et enfoi pour lancer les tests:

Pour lancer tous les tests

```shell
bin/phpunit
```

Pour lancer certains tests

```shell
bin/phpunit --filter testHome
```

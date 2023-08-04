<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Season;
use App\Entity\Casting;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Provider\OflixProvider;
use App\Entity\User;
use App\Service\MySlugger;
use App\Service\OmdbApi;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $slugger;
    private $connection;
    private $userPasswordHasher;
    private $omdbApi;

    public function __construct(
        Connection $connection,
        UserPasswordHasherInterface $userPasswordHasher,
        MySlugger $slugger,
        OmdbApi $omdbApi
    )
    {
        // On récupère la connexion à la BDD (DBAL ~= PDO)
        // pour exécuter des requêtes manuelles en SQL pur
        $this->connection = $connection;
        $this->slugger = $slugger;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->omdbApi = $omdbApi;
    }
    
    /**
     * Permet de TRUNCATE les tables et de remettre les AI à 1
     */
    private function truncate()
    {
        // On passe en mode SQL ! On cause avec MySQL
        // Désactivation la vérification des contraintes FK
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        // On tronque
        $this->connection->executeQuery('TRUNCATE TABLE casting');
        $this->connection->executeQuery('TRUNCATE TABLE genre');
        $this->connection->executeQuery('TRUNCATE TABLE movie');
        $this->connection->executeQuery('TRUNCATE TABLE movie_genre');
        $this->connection->executeQuery('TRUNCATE TABLE person');
        $this->connection->executeQuery('TRUNCATE TABLE season');
        // etc.
    }

    public function load(ObjectManager $manager): void
    {
        // on aimerati "reset" les id de nos données à 1
        // cela est possible avec la commande SQL "TRUNCATE"
        // la commande de fixtures permet de faire un "--purge-with-truncate"
        // sauf qu'on ne peut pas gérer les suppressions en CASCADE
        // donc les contraintes sur les clés étangères s'appliquent et ça ne fonctionne pas

        // on va contourner la chose en créant notre propre TRUNCATE
        $this->truncate();

        // on instancie la librairie Faker, en français
        // @see https://fakerphp.github.io/#localization
        $faker = Factory::create('fr_FR');

        // pour générer les mêmes données à chaque fois, on renseigne la "seed"
        // @see https://fakerphp.github.io/#seeding-the-generator
        // ces chiffres ne correspondent à rien de particulier
        $faker->seed(4586731294);

        // on donne notre Provider à Faker, ce qui va nous permettre de bénéficier
        // de fonctionnalités de Faker avec nos données à nous (par ex. unique())
        // @see https://fakerphp.github.io/#faker-internals-understanding-providers
        $faker->addProvider(new OflixProvider());

        // Users
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        // Hashage du password avec l'instance 'UserPasswordHasherInterface'
        // récupérée lors de l'appel au constructeur de la classe avec
        // L'injection de dépendances
        $admin->setPassword(
            $this->userPasswordHasher->hashPassword($admin, 'admin')
        );
        $manager->persist($admin);

        $managerUser = new User();
        $managerUser->setEmail('manager@manager.com');
        $managerUser->setRoles(['ROLE_MANAGER']);
        $managerUser->setPassword(
            $this->userPasswordHasher->hashPassword($managerUser, 'manager')
        );
        // Attention $manager = le Manager de Doctrine :D
        $manager->persist($managerUser);

        $user = new User();
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, 'user')
        );
        $manager->persist($user);

        // 20 genres
        // un tableau pour les réutiliser sur les films
        $genreList = [];

        for ($g = 1; $g <= 10; $g++) { 
            $genre = new Genre();
            // on utilise le "modifier" "unique()" qui permet de retourner des valeurs uniques pour les genres
            // @see https://fakerphp.github.io/#modifiers
            $genre->setName($faker->unique()->movieGenre());
            // on persist
            $manager->persist($genre);
            // on ajoute à la liste des genres
            $genreList[] = $genre; // array_push($genreList, $genre);
        }
        // dd($genreList);

        // Persons

        // Tableau pour nos persons
        $personList = [];

        for ($i = 1; $i <= 200; $i++) {

            // Nouvelle Person
            $person = new Person();
            $person->setFirstname($faker->firstName());
            $person->setLastname($faker->lastName());

            // On l'ajoute à la liste pour usage ultérieur
            $personList[] = $person;

            // On persiste
            $manager->persist($person);
        }

        // 20 films
        $m = 1;
        while ($m <= 20) { 
            // on crée une entité
            $movie = new Movie();
            
            // on renseigne ses propriétés
            // on utilise le "modifier" "unique()" qui permet de retourner des valeurs uniques pour les titres
            // @see https://fakerphp.github.io/#modifiers
            $movie->setTitle($faker->unique()->movieTitle());

            // Recherche sur omdb
            $movieOmdb = $this->omdbApi->fetch($movie->getTitle());
            if ($movieOmdb) {
                $m++;
                $movie->setPoster($movieOmdb['Poster']);
                $movie->setSynopsis($movieOmdb['Plot']);
                $movie->setSummary($movieOmdb['Plot']);
                $movie->setDuration(intval($movieOmdb['Runtime']));
            } else {
                $movie->setPoster('https://picsum.photos/id/'.mt_rand(1, 100).'/300/450');
                $movie->setSynopsis($faker->text(500));
                $movie->setSummary($faker->text(150));
                $movie->setDuration($faker->numberBetween(30, 240));
            }
            $movie->setType($faker->randomElement(['Film', 'Série']));

            // 'Film' ou 'Série' ? 1 chance sur 2
            // on choisit un type au hasard
            //$type = mt_rand(1, 2) == 1 ? 'Film' : 'Série';
            // on le définit pour ce film
            $movie->setRealeaseDate($faker->dateTimeBetween('-100 years', '+10 years'));

            $movie->setRating($faker->numberBetween(1, 5));

            $movie->setSlug($this->slugger->slugify($movie->getTitle()));

            // on crée les saisons pour une série
            if ($movie->getType() === 'Série') {
                // on crée une boucle for avec un numéro aléatoire dans la condition pour déterminer le nombre de saisons
                // mt_rand() ne sera exécuté qu'une fois en début de boucle
                for ($s = 1; $s <= mt_rand(3, 8); $s++) {
                    // On créé la nouvelle entitée Season
                    $season = new Season();
                    // On insert le numéro de la saison en cours $s
                    $season->setSeasonNumber($s);
                    // On insert un numéro d'épisode aléatoire
                    $season->setEpisodesNumber($faker->numberBetween(6, 24));
                    // Puis on associe la saison à la série courante
                    $season->setMovie($movie);
                    // On persite la saison
                    $manager->persist($season);
                }
            }

            // astuce pour éviter les doublons et avoir le nombre exact de genres
            // on mélange le tableau de genres
            shuffle($genreList);
            // on associe de 1 à 3 genres par film
            for ($g = 1; $g <= mt_rand(1, 3); $g++) {
                // solution 1
                // $randomGenre = $genreList[mt_rand(0, count($genreList) - 1)];
                // solution 2
                // avec shuffle(), on prend les 3 premiers
                $randomGenre = $genreList[$g];
                // on associe
                $movie->addGenre($randomGenre);
            }

            // On ajoute de 3 à 5 castings par films au hasard pour chaque film

            // on mélange les Persons
            shuffle($personList);

            for ($c = 1; $c <= mt_rand(3, 5); $c++) {

                $casting = new Casting();

                // Les propriétés role et creditOrder
                $casting->setRole($faker->name());
                $casting->setCreditOrder($c);

                // Les 2 associations
                // Movie
                $casting->setMovie($movie);
                // Person
                // On pioche les index fixes 1, 2, 3, ... avec le shuffle()
                $randomPerson = $personList[$c];
                $casting->setPerson($randomPerson);

                // on persiste
                $manager->persist($casting);
            }

            // on persist
            $manager->persist($movie);
        }

        $manager->flush();
    }
}

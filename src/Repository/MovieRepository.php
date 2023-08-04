<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
     * Cette méthode permet de rechercher dans la database des films
     * avec un système de pagination, on peut spécifier dans les paramètres
     *  - Vecteur de recherche sur le titre des movies (wildcard %)
     *  - page a récupérer
     *  - nombre d'éléments par page
     *
     * @param string $search
     * @param integer $page
     * @param integer $nbPerPage
     * @return void
     */
    public function paginatedFind($search='%', $page=1, $nbPerPage=null)
    {
        // Nécessaire pour le createQuery
        $em = $this->getEntityManager();

        // 1ere étape, recherche du nombre d'éléments a paginer
        // Count(*) avec la clause where
        $count = $em->createQuery(
            "SELECT COUNT(m)
            FROM \App\Entity\Movie m
            WHERE m.title LIKE :search"
        )
        ->setParameter('search', $search)
        ->getSingleScalarResult();

        $nbPages = $nbPerPage === null ? 1 : intval(ceil($count / $nbPerPage));

        if ($page > $nbPages) {
            $page = 1;
        }

        // 2eme étape, aller chercher dans la db les éléments
        $query = $em->createQuery(
            "SELECT m
            FROM \App\Entity\Movie m
            WHERE m.title LIKE :search
            ORDER BY m.id ASC"
        )
        ->setParameter('search', $search);

        // dans les limites de 'offset' et 'limit' spécifiés
        // par setMaxResults -> limit
        // et par setFirstResult -> offset
        if ($nbPerPage !== null) {
            $query->setMaxResults($nbPerPage);
            $query->setFirstResult($nbPerPage * ($page-1));
        }

        // Interroge la database
        return [
            'search'       => $search,
            'count'        => $count,
            'nbPerPage'    => $nbPerPage,
            'nbPages'      => $nbPages,
            'currentPage'  => $page,
            'movies'       => $query->getResult()
        ];
    }
    /**
     * Récupere les 10 films les plus récents
     * Requête est développée en utilisant QueryBuilder
     *
     * @return void
     */
    public function findLatestByReleaseDateQb()
    {
        // createQueryBuilder effectue un 'select * from Movie' (entité du repository)
        return $this->createQueryBuilder('m')
        // Ajout de la 'order by'
                    ->orderBy('m.realeaseDate', 'desc')
        // Ajout de 'limit 10'
                    ->setMaxResults(10)
        // Construit une requête exploitable
                    ->getQuery()
        // Interroge la database
                    ->getResult();
    }

    /**
     * Récupere les 10 films les plus récents
     * Requête est développée en utilisant QueryBuilder
     *
     * @return void
     */
    public function findLatestByReleaseDateDQL(int $limit = 5)
    {
        $em = $this->getEntityManager();

        // createQueryBuilder effectue un 'select * from Movie' (entité du repository)
        // Ajout de la 'order by'
        $query = $em->createQuery(
            'SELECT m
            FROM \App\Entity\Movie m
            ORDER BY m.realeaseDate DESC'
        );
        // Ajout de 'limit 10'
        // Interroge la database
        return $query->setMaxResults($limit)->getResult();
    }

    public function add(Movie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Movie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Cette fonction du repository permet de chercher un film
     * au hasard dans la base
     *
     * @return array
     */
    public function getOneRandomMovie()
    {
        // On effectue la requete en SQL pour avoir accès facilement
        // Aux fonctions SQL telles que random()

        // Récupération de la connection database
        $connection = $this->getEntityManager()->getConnection();

        // Select d'un film au hasard (classement random) +
        // LIMIT 1
        $query = "SELECT id, title, slug FROM movie
                    ORDER BY RAND()
                    LIMIT 1";

        // On retourne le film séléctionné
        $result = $connection->executeQuery($query);

        return $result->fetchAssociative();
    }

//    /**
//     * @return Movie[] Returns an array of Movie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Movie
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

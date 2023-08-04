<?php

namespace App\Repository;

use App\Entity\Casting;
use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Casting>
 *
 * @method Casting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Casting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Casting[]    findAll()
 * @method Casting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CastingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Casting::class);
    }

    /**
     * Rechercher tous les roles d'un film en utilisant des jointures
     * Avec QueryBuilder
     *
     * @param Casting $entity
     * @param boolean $flush
     * @return void
     */
    public function findAllCastingsByMovieQB(Movie $movie)
    {
        // Je construis avec QueryBuilder
        return $this->createQueryBuilder('casting')
        // Je joint l'entité casting.person -> App\Entity\Person
                    ->innerJoin('casting.person', 'person')
                    ->addSelect('person')
        // Je veux selectionner uniquement les castings d'un movie particulier
                    // Définition du critère de selection (casting.movie)
                    // Définition du parametre (:movie)
                    ->where('casting.movie = :movie')
                    // Je dis que je veux selectionner les castings se référant
                    // au '$movie' passé en paramètre
                    ->setParameter('movie', $movie)

                    ->orderBy('casting.creditOrder', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
    /**
     * Rechercher tous les roles d'un film en utilisant des jointures
     * Avec QueryBuilder
     *
     * Cette requête peremt d'éviter l'hydratation tardive des données
     * de l'entité Person. De 3 requetes au départ on fait plus qu'une
     * 
     * @param Casting $entity
     * @param boolean $flush
     * @return void
     */
    public function findAllCastingsByMovieDQL(Movie $movie)
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT c, p FROM App\Entity\Casting c
             INNER JOIN c.person p
             WHERE c.movie = :movie
             ORDER BY c.creditOrder ASC'
        )->setParameter('movie', $movie);
        
        return $query->getResult();
    }

    public function add(Casting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Casting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Casting[] Returns an array of Casting objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Casting
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

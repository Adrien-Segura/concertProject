<?php

namespace App\Repository;

use App\Entity\Concert;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Concert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Concert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Concert[]    findAll()
 * @method Concert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConcertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Concert::class);
    }

    // /**
    //  * @return Concert[] Returns an array of Concert objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Concert
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    // /**
    //  * @return Concert[] Returns an array of Concert objects
    //  */
    public function findAllPassed()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.date < :date')
            ->setParameter('date', new DateTime())
            ->orderBy('c.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllComing()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.date > :date')
            ->setParameter('date', new DateTime())
            ->orderBy('c.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllComingByPage($page, $limit)
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.date > :date')
            ->setParameter('date', new DateTime())
            ->orderBy('c.date', 'ASC')
            ->getQuery();
        $paginator = new Paginator($query);

        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }

    public function findAllComingByBand($band_id)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.bands', 'b')
            ->andWhere('c.date > :date')
            ->andWhere('b.id = :b_id')
            ->setParameter('date', new DateTime())
            ->setParameter('b_id', $band_id)
            ->orderBy('c.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

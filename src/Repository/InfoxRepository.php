<?php

namespace App\Repository;

use App\Entity\Infox;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Infox|null find($id, $lockMode = null, $lockVersion = null)
 * @method Infox|null findOneBy(array $criteria, array $orderBy = null)
 * @method Infox[]    findAll()
 * @method Infox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Infox::class);
    }

      /**
      * @return Infox[] Returns an array of Infox objects
      */
    public function findByTheme($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.theme = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Infox[] Returns an array of Infox objects
     */
    public function findByPersonne($value)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.like', 'l')
            ->andWhere('l.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
            ;
    }


    // /**
    //  * @return Infox[] Returns an array of Infox objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Infox
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

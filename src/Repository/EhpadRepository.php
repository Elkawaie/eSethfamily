<?php

namespace App\Repository;

use App\Entity\Ehpad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ehpad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ehpad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ehpad[]    findAll()
 * @method Ehpad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EhpadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ehpad::class);
    }

    // /**
    //  * @return Ehpad[] Returns an array of Ehpad objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ehpad
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

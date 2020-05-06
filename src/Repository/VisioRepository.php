<?php

namespace App\Repository;

use App\Entity\Visio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Visio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visio[]    findAll()
 * @method Visio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visio::class);
    }

    /**
     * @return Visio[] Returns an array of Visio objects
     */
    public function findVisoByEhpad($id, $etat)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.resident', 'Resident')
            ->where('Resident.ehpad = :id')
            ->andWhere('u.actif = :etat')
            ->setParameter('id', $id)
            ->setParameter('etat', $etat)
            ->orderBy('u.start_at', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findVisoByResident($id)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.resident', 'Resident')
            ->where('Resident.id = :id')
            ->andWhere('u.actif = :etat')
            ->setParameter('id', $id)
            ->setParameter('etat', true)
            ->orderBy('u.start_at', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    /*
    public function findOneBySomeField($value): ?Visio
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\DemandeAdd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DemandeAdd|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeAdd|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeAdd[]    findAll()
 * @method DemandeAdd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeAddRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeAdd::class);
    }

     /**
      * @return DemandeAdd[] Returns an array of DemandeAdd objects
      */
    public function findDemandeByEhpad($id, $str)
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.demandeur', 'Famille')
            ->leftJoin('Famille.ehpads', 'Ehpad')
            ->where('Ehpad = :id')
            ->andWhere('d.sujet = :str')
            ->setParameter('id', $id)
            ->setParameter('str', $str)
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?DemandeAdd
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

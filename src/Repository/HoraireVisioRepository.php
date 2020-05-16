<?php

namespace App\Repository;

use App\Entity\HoraireVisio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method HoraireVisio|null find($id, $lockMode = null, $lockVersion = null)
 * @method HoraireVisio|null findOneBy(array $criteria, array $orderBy = null)
 * @method HoraireVisio[]    findAll()
 * @method HoraireVisio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HoraireVisioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HoraireVisio::class);
    }

    /**
     * @return HoraireVisio[] Returns an array of HoraireVisio objects
     * @throws Exception
     */
    public function findHoraireByEhpadAndDate($debut, $fin, $id)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.ehpad', 'Ehpad')
            ->where('u.debut BETWEEN :dateMin AND :dateMax')
            ->andWhere('Ehpad.id = :id')
            ->setParameter('dateMin', $debut)
            ->setParameter('dateMax', $fin)
            ->setParameter('id', $id)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByDateAndEhpad($id, $datetime)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.ehpad', 'Ehpad')
            ->where('u.debut >= :datetime')
            ->andWhere('Ehpad.id = :id')
            ->setParameter('id', $id)
            ->setParameter('datetime', $datetime)
            ->orderBy('u.debut', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?HoraireVisio
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

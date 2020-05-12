<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    public  function findByFamille_Ehpad($id)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.fkFamille', 'Famille')
            ->leftJoin('Famille.resident', 'Resident')
            ->where('Resident.ehpad = :id')
            ->setParameter('id', $id)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findEmailsEmployeByEhpads($ids){

        return $this->createQueryBuilder('u')
            ->select('u.email')
            ->leftJoin('u.ehpad', 'Ehpad')
            ->where('Ehpad.id IN (:ids)')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('ids', $ids)
            ->setParameter('role', '%ROLE_EMPLOYE%')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findEmailsEmployeByEhpad($id){

        return $this->createQueryBuilder('u')
            ->select('u.email')
            ->leftJoin('u.ehpad', 'Ehpad')
            ->where('Ehpad.id = :id')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('id', $id)
            ->setParameter('role', '%ROLE_EMPLOYE%')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByUnactif($actif, $id)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.fkFamille', 'Famille')
            ->leftJoin('Famille.ehpads', 'Ehpad')
            ->where('u.actif = :actif')
            ->andWhere('Ehpad.id = :id')
            ->setParameter('actif', $actif)
            ->setParameter('id', $id)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

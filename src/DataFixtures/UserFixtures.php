<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->encoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        for($a =0; $a <10; $a++){
            $user =new User();
            $user->setEmail(sprintf('userFamilly%d@symfony.com',$a));
            $password = 'userFamilly';
            $user->setPassword($this->encoder->encodePassword($user, (string)$password));
            $user->setRoles(array_unique(['ROLE_FAMILLY']));
            $user->setActif(true);
            $manager->persist($user);
        }
        for($b =0; $b <10; $b++){
            $user =new User();
            $user->setEmail(sprintf('userResident%d@symfony.com',$b));
            $password = 'userResident';
            $user->setPassword($this->encoder->encodePassword($user, (string)$password));
            $user->setRoles(array_unique(['ROLE_RESIDENT']));
            $user->setActif(true);
            $manager->persist($user);
        }
        for($c =0; $c <10; $c++){
            $user =new User();
            $user->setEmail(sprintf('userEmploye%d@symfony.com',$c));
            $password = 'userEmploye';
            $user->setPassword($this->encoder->encodePassword($user, (string)$password));
            $user->setRoles(array_unique(['ROLE_EMPLOYE']));
            $user->setActif(true);
            $manager->persist($user);
        }
        for($d =0; $d <10; $d++){
            $user =new User();
            $user->setEmail(sprintf('userAdmin%d@symfony.com',$d));
            $password = 'userAdmin';
            $user->setPassword($this->encoder->encodePassword($user, (string)$password));
            $user->setRoles(array_unique(['ROLE_ADMIN']));
            $user->setActif(true);
            $manager->persist($user);
        }


        $manager->flush();
    }
}

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
            $user =new User();
            $user->setEmail(sprintf('admin@esethfamily.com'));
            $password = 'admin1234567';
            $user->setPassword($this->encoder->encodePassword($user, (string)$password));
            $user->setRoles(array_unique(['ROLE_ADMIN']));
            $user->setActif(true);
            $manager->persist($user);

        $manager->flush();
    }
}

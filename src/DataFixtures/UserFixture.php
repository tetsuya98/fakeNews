<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setUsername('use');
        $user1->setRoles(['ROLE_USER']);
        $encrypted = $this->passwordEncoder->encodePassword($user1,'use');
        $user1->setPassword($encrypted);
        $manager->persist($user1);
        $user2 = new User();
        $user2->setUsername('mod');
        $user2->setRoles(['ROLE_MODERATEUR']);
        $encrypted = $this->passwordEncoder->encodePassword($user2,'mod');
        $user2->setPassword($encrypted);
        $manager->persist($user2);
        $user3 = new User();
        $user3->setUsername('dir');
        $user3->setRoles(['ROLE_DIRECTEUR']);
        $encrypted = $this->passwordEncoder->encodePassword($user3,'dir');
        $user3->setPassword($encrypted);
        $manager->persist($user3);

        $manager->flush();
    }
}

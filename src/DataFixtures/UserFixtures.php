<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user-alex';

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstName("Alexandre")
            ->setLastName("Bonnin")
            ->setEmail("bonnin.a.k@gmail.com")
            ->setPassword("password")
            ->setRegisteredAt(new \DateTime('now'))
        ;
        $this->addReference(self::USER_REFERENCE, $user);
        $manager->persist($user);

        $manager->flush();
    }
}

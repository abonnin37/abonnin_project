<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserFixtures extends Fixture implements ContainerAwareInterface
{
    public const USER_REFERENCE = 'user-alex';

    private ?ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager): void
    {
        $passwordHasher = $this->container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setFirstName('Alexandre')
            ->setLastName('Bonnin')
            ->setEmail('bonnin.a.k@gmail.com')
            ->setRegisteredAt(new \DateTime('now'))
            ->setVerified(true)
            ->setRoles(['ROLE_ADMIN'])
        ;

        $hashedPassword = $passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $this->addReference(self::USER_REFERENCE, $user);
        $manager->persist($user);

        $manager->flush();
    }
}

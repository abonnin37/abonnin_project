<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $project1 = new Project();
        $project1->setName("Stagiaire en développement web")
            ->setBeginAt(new \DateTime("2020-02-29"))
            ->setEndAt(new \DateTime("2020-07-31"))
            ->setExcerpt("Conception, réalisation et déploiement d’un agenda partagé en C# et .NET. Rédaction d’un business plan pour la création d’une ONG dans le secteur de la nutrition animale.")
            ->setUser($this->getReference(UserFixtures::USER_REFERENCE))
        ;
        $manager->persist($project1);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
          UserFixtures::class,
        );
    }
}

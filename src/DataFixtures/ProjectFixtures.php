<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $project1 = new Project();
        $project1->setName('Stagiaire en développement web')
            ->setBeginAt(new \DateTime('2020-02-29'))
            ->setEndAt(new \DateTime('2020-07-31'))
            ->setExcerpt('Conception, réalisation et déploiement d\'un agenda partagé en C# et .NET. Rédaction d\'un business plan pour la création d\'une ONG dans le secteur de la nutrition animale.')
            ->setDescription('Pendant mon stage, j\'ai eu l\'opportunité de travailler sur deux projets distincts :

1. Développement d\'un agenda partagé en C# et .NET :
- Conception de l\'architecture de l\'application
- Implémentation des fonctionnalités de base (CRUD)
- Mise en place de l\'authentification
- Déploiement sur un serveur Windows

2. Rédaction d\'un business plan pour une ONG :
- Analyse du marché de la nutrition animale
- Étude de la concurrence
- Élaboration d\'un plan financier
- Définition des objectifs et des stratégies')
            ->setUser($this->getReference(UserFixtures::USER_REFERENCE))
        ;
        $manager->persist($project1);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}

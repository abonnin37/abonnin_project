<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @return Project[]
     */
    public function findLatest(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.begin_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Project[]
     */
    public function findByTechnology(string $technology): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.technologies', 't')
            ->andWhere('t.name = :technology')
            ->setParameter('technology', $technology)
            ->orderBy('p.begin_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Project[]
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.end_at IS NULL OR p.end_at > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('p.begin_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

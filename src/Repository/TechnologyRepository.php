<?php

namespace App\Repository;

use App\Entity\Technology;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Technology>
 */
class TechnologyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technology::class);
    }

    /**
     * @return Technology[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Technology[]
     */
    public function findByProject(int $projectId): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.projects', 'p')
            ->andWhere('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

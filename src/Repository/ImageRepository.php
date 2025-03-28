<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * @return Image[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Image[]
     */
    public function findByProject(int $projectId): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.project', 'p')
            ->andWhere('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->orderBy('i.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Image[]
     */
    public function findLatest(int $limit = 10): array
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}

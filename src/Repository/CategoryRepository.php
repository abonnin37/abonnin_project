<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return Category[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Category[]
     */
    public function findByPost(int $postId): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.posts', 'p')
            ->andWhere('p.id = :postId')
            ->setParameter('postId', $postId)
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

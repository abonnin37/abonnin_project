<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @return Tag[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Tag[]
     */
    public function findByPost(int $postId): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.posts', 'p')
            ->andWhere('p.id = :postId')
            ->setParameter('postId', $postId)
            ->orderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

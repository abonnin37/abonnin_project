<?php

namespace App\Repository;

use App\Entity\ProspectMail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProspectMail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProspectMail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProspectMail[]    findAll()
 * @method ProspectMail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProspectMailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProspectMail::class);
    }

    // /**
    //  * @return ProspectMail[] Returns an array of ProspectMail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProspectMail
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

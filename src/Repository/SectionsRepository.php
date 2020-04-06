<?php

namespace App\Repository;

use App\Entity\Sections;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sections|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sections|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sections[]    findAll()
 * @method Sections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sections::class);
    }

    // /**
    //  * @return Sections[] Returns an array of Sections objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sections
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

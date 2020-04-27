<?php

namespace App\Repository;

use App\Entity\Types;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Types|null find($id, $lockMode = null, $lockVersion = null)
 * @method Types|null findOneBy(array $criteria, array $orderBy = null)
 * @method Types[]    findAll()
 * @method Types[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Types::class);
    }

    /**
     * @param $nom
     * @return Types|null
     * @throws NonUniqueResultException
     */
    public function findOneByNom($nom)
    {
        return $this->createQueryBuilder('t')
            ->where('t.nom = :nom')->setParameter('nom', $nom)
            ->getQuery()->getOneOrNullResult();
    }

    // /**
    //  * @return Types[] Returns an array of Types objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Types
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

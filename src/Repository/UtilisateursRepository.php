<?php

namespace App\Repository;

use App\Entity\Utilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Utilisateurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateurs[]    findAll()
 * @method Utilisateurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateurs::class);
    }

    public function findOneByLogin($login): ?Utilisateurs
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.section', 'section')->addSelect('section')
            ->leftJoin('u.grade', 'grade')->addSelect('grade')
            ->where('u.login = :login')->setParameter('login', $login)
            ->getQuery()->getOneOrNullResult();
    }

    public function findOneByLoginAndPassword($login, $password): ?Utilisateurs
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.section', 'section')->addSelect('section')
            ->leftJoin('u.grade', 'grade')->addSelect('grade')
            ->andWhere('u.login = :login')->setParameter('login', $login)
            ->andWhere('u.mot_de_passe = :pwd')->setParameter('pwd', $password)
            ->getQuery()->getOneOrNullResult();
    }

    // /**
    //  * @return Utilisateurs[] Returns an array of Utilisateurs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Utilisateurs
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

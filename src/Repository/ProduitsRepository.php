<?php

namespace App\Repository;

use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Produits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produits[]    findAll()
 * @method Produits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }

    /**
     * Renvoie le nombre de produits selon leurs type
     *
     * @param $type : Le type de produit
     * @return int
     */
    public function countByType($type)
    {
        return count($this->findByType($type));
    }

    /**
     * Renvoie la liste des produits selon leurs type
     *
     * @param $type : Le type de produit
     * @return Produits[]
     */
    public function findByType($type)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.type', 'type')->addSelect('type')
            ->where('type.nom = :t')->setParameter('t', $type)
            ->getQuery()->getResult();
    }

    /**
     * Renvoie la liste des produits selon leurs type, avec une pagination
     *
     * @param $type : Le type de produit
     * @param $page : La pagination en commencant à 0
     * @return Produits[]
     */
    public function findByTypeWithPagination($type, $page)
    {
        $nb = 10;
        return $this->createQueryBuilder('p')
            ->leftJoin('p.type', 'type')->addSelect('type')
            ->where('type.nom = :t')->setParameter('t', $type)
            ->setFirstResult($page * $nb)->setMaxResults($nb)
            ->getQuery()->getResult();
    }

    /**
     * Cherche dans tous les produits
     *
     * @param $keyword : Le mot clé pour la recherche
     * @return Produits[]
     */
    public function findLikeInType($keyword)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.type', 'type')->addSelect('type')
            ->where('p.nom LIKE :kw')->setParameter('kw', '%' . $keyword . '%')
            ->getQuery()->getResult();
    }

    // /**
    //  * @return Produits[] Returns an array of Produits objects
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
    public function findOneBySomeField($value): ?Produits
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

<?php

namespace App\Repository;

use App\Entity\Commandes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commandes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commandes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commandes[]    findAll()
 * @method Commandes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commandes::class);
    }

    /**
     * @param $type : Le nom du type
     * @return Commandes[] : La liste des commandes de type $type
     */
    private function findAllTypeSortByDate($type)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.produit', 'produit')->addSelect('produit')
            ->leftJoin('produit.type', 'type')->addSelect('type')
            ->where('type.nom = :nom')->setParameter('nom', $type)
            ->orderBy('c.date', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @return Commandes[]
     */
    public function findAllVesselSortByDate()
    {
        return $this->findAllTypeSortByDate('vaisseau');
    }

    /**
     * @return Commandes[]
     */
    public function findAllProgrammeSortByDate()
    {
        return $this->findAllTypeSortByDate('programme');
    }

    /**
     * @return Commandes[]
     */
    public function findAllMercenaireSortByDate()
    {
        return $this->findAllTypeSortByDate('mercenaire');
    }

    // /**
    //  * @return Commandes[] Returns an array of Commandes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Commandes
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

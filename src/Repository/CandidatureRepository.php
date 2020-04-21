<?php

namespace App\Repository;

use App\Entity\Candidature;
use App\Entity\Sections;
use App\Entity\Utilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Candidature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Candidature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Candidature[]    findAll()
 * @method Candidature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candidature::class);
    }

    /**
     * Renvoie toutes les candidatures non acceptées
     *
     * @return Candidature[]
     */
    public function findAllNonAccepted()
    {
        return $this->createQueryBuilder('c')
            ->where('c.accepte = :accept')->setParameter('accept', false)
            ->getQuery()->getResult();
    }

    /**
     * Renvoie toutes les candidature non acceptées ou le candidat demande à être dans une section
     *
     * @param $nom L'id de la section
     * @return Candidature[]
     */
    public function findAllNonAcceptedBySection($nom)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.section', 'section')->addSelect('section')
            ->andWhere('c.accepte = :accept')->setParameter('accept', false)
            ->andWhere('section.nom = :nom')->setParameter('nom', $nom)
            ->getQuery()->getResult();
    }

    /**
     * Supprime une candidature
     *
     * @param $id
     * @return int|mixed|string
     */
    public function reject($id)
    {
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.id = :id')->setParameter('id', $id)
            ->getQuery()->getResult();
    }

    /**
     * Accepte la candidature, et ajoute l'utilisateur
     *
     * @param $id
     * @return int|mixed|string
     */
    public function accept($id)
    {
        $candidature = $this->createQueryBuilder('c')
            ->where('c.id = :id')->setParameter('id', $id)
            ->getQuery()->getOneOrNullResult();
        $em = $this->getEntityManager();
        $user = new Utilisateurs();
        $user->setLogin($candidature->getLogin())
            ->setMotDePasse($candidature->getMotDePasse())
            ->setPrenom($candidature->getPrenom())
            ->setNom($candidature->getNom())
            ->setSection($candidature->getSection());
        if ($candidature->getSection()) {
            $section = $em->getRepository(Sections::class)->findOneByNom($candidature->getSection()->getNom());
            $user->setSection($section);
        }
        $em->persist($user);
        $em->flush();

        // On accepte la candidature
        return $this->createQueryBuilder('c')
            ->update()
            ->set('c.accepte', true)
            ->where('c.id = :id')->setParameter('id', $id)
            ->getQuery()->getResult();
    }

    // /**
    //  * @return Candidature[] Returns an array of Candidature objects
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
    public function findOneBySomeField($value): ?Candidature
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

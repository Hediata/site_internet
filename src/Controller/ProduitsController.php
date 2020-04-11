<?php


namespace App\Controller;


use App\Entity\Produits;
use App\Entity\Types;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produits")
 */
class ProduitsController extends AbstractController
{
    /**
     * @Route("/", name="app_produits")
     */
    public function market(EntityManagerInterface $em)
    {
        $repositoryT = $em->getRepository(Types::class);
        $type = $repositoryT->findOneBy(['nom' => 'vaisseau']);
        $repositoryP = $em->getRepository(Produits::class);
        $vaisseaux = $repositoryP->findBy(['type' => $type->getId()]);

        return $this->render('produits/produits.html.twig', [
            'vaisseaux' => $vaisseaux,
        ]);
    }

    /**
     * @Route("/vessel/{id}", name="app_show_vessel")
     */
    public function showVessel($id, EntityManagerInterface $em)
    {
        $repositoryP = $em->getRepository(Produits::class);
        $vaisseau = $repositoryP->find($id);

        if (!$vaisseau)
        {
            throw $this->createNotFoundException(sprintf('No vessel found for id : %s', $id));
        }

        return $this->render('produits/show_vessel.html.twig', [
            'vaisseau' => $vaisseau,
        ]);
    }
}
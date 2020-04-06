<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produits")
 */
class ProduitsController extends AbstractController
{
    /**
     * @Route("/", name="app_produits")
     */
    public function home()
    {
        $vaisseaux = [
            ['nom' => 'test', 'image' => '/images/Hediata.png', 'prix' => 3000, 'description' => 'Logo de la faction'],
            ['nom' => 'flotte_de_100_chasseurs', 'image' => '/images/fond_header.jpg', 'prix' => 1000000, 'description' => 'Très pratique pour attaquer des personnes'],
        ];

        return $this->render('produits/produits.html.twig', [
            'vaisseaux' => $vaisseaux,
        ]);
    }

    /**
     * @Route("/vessel/{name}", name="app_show_vessel")
     */
    public function showVessel($name)
    {

        return $this->render('produits/show_vessel.html.twig', [
            'name' => $name,
        ]);
    }
}
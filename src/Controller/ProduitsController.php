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
            [
                'id' => 1,
                'nom' => 'flotte_de_100_vaisseaux',
                'prix' => 150450,
                'description' => 'Il s\'agit d\'une flotte de 100 chasseurs bien armés, avec toutes leurs munitions.',
                'image' => '/images/fond_header.jpg',
                'date_creation' => '2020-02-25',
            ],
            [
                'id' => 2,
                'nom' => 'cuirassé_en_fin_de_vie',
                'prix' => 300,
                'description' => 'Ce cuirassé a déjà servi pour plusieurs batailles. Il a dû être rafistolé dans tous les sens, mais il tient bien. Malheureusement les munitions pour son armement ne sont plus produites car obsolètes.',
                'image' => '/images/Hediata.png',
                'date_creation' => '2020-02-25',
            ],
        ];

        return $this->render('produits/produits.html.twig', [
            'vaisseaux' => $vaisseaux,
        ]);
    }

    /**
     * @Route("/vessel/{id}", name="app_show_vessel")
     */
    public function showVessel($id)
    {
        $vaisseau = [
            'id' => $id,
            'nom' => 'cuirassé_en_fin_de_vie',
            'prix' => 300,
            'description' => 'Ce cuirassé a déjà servi pour plusieurs batailles. Il a dû être rafistolé dans tous les sens, mais il tient bien. Malheureusement les munitions pour son armement ne sont plus produites car obsolètes.',
            'image' => '/images/Hediata.png',
            'date_creation' => '2020-02-25',
        ];

        return $this->render('produits/show_vessel.html.twig', [
            'vaisseau' => $vaisseau,
        ]);
    }
}
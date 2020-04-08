<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {
        return $this->render('home/home.html.twig');
    }

    /**
     * @Route("/connexion", name="app_connexion")
     */
    public function connexion()
    {
        return $this->render('home/connexion.html.twig');
    }

    /**
     * @Route("/rejoindre/{section}", name="app_rejoindre", defaults={"section"="none"})
     */
    public function rejoindre($section)
    {
        return $this->render('home/rejoindre.html.twig', [
            'section' => $section,
        ]);
    }

    /**
     * @Route("/moi", name="app_user")
     */
    public function user()
    {
        $user = [
            'login' => 'Gashmob',
            'mdp' => 'password',
            'prenom' => 'firstName',
            'nom' => 'lastName',
        ];

        return $this->render('home/user.html.twig', [
            'user' => $user,
        ]);
    }
}
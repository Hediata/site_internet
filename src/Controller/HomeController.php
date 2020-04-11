<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

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
        $this->session->set('connected', true);

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
     * @Route("/user/{moi}", name="app_user", defaults={"moi"=""})
     */
    public function user($moi)
    {
        $user = [
            'login' => $moi,
            'mdp' => 'password',
            'prenom' => 'firstName',
            'nom' => 'lastName',
            'section' => 'Industry',
            'grade' => 'Superviseur Général',
        ];

        return $this->render('home/user.html.twig', [
            'user' => $user,
        ]);
    }
}
<?php


namespace App\Controller;


use App\Entity\Sections;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(EntityManagerInterface $em)
    {
        $repositoryS = $em->getRepository(Sections::class);
        $sections = $repositoryS->findAll();

        return $this->render('home/home.html.twig', [
            'sections' => $sections,
        ]);
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
     * @Route("/user/{login}", name="app_user", defaults={"login"=""})
     */
    public function user($login, EntityManagerInterface $em)
    {
        $repositoryU = $em->getRepository(Utilisateurs::class);

        if ($login === "")
        {
            $login = "gashmob";
        }
        $user = $repositoryU->findOneBy(['login' => $login]);

        if (!$user)
        {
            throw $this->createNotFoundException(sprintf('No user found for login : %s', $login));
        }

        return $this->render('home/user.html.twig', [
            'user' => $user,
        ]);
    }
}
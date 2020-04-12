<?php


namespace App\Controller;


use App\Entity\Sections;
use App\Entity\Utilisateurs;
use App\Form\CandidatureFormType;
use App\Form\ConnexionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function connexion(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(ConnexionFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $repositoryU = $em->getRepository(Utilisateurs::class);
            $user = $repositoryU->findOneBy([
                'login' => $data->getLogin(),
                'mot_de_passe' => $data->getMotDePasse(),
            ]);

            if ($user)
            {
                $this->session->set('user', $user);
                $this->addFlash('success', 'Vous êtes connecté(e) !');
                return $this->redirectToRoute('app_user', ['login' => $user->getLogin()]);
            }
        }

        return $this->render('home/connexion.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/deconnexion", name="app_deconnexion")
     */
    public function deconnexion()
    {
        $this->session->remove('user');

        return $this->redirectToRoute('app_homepage');
    }

    /**
     * @Route("/rejoindre/", name="app_rejoindre")
     */
    public function rejoindre(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(CandidatureFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $candidature = $form->getData();

            if ($candidature->getSection())
            {
                $repositoryS = $em->getRepository(Sections::class);
                $section = $repositoryS->findOneBy([
                    'nom' => $candidature->getSection()->getNom(),
                ]);
                $candidature->setSection($section);
            }

            $em->persist($candidature);
            $em->flush();

            $this->addFlash('success', 'Candidature envoyée');
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('home/rejoindre.html.twig', [
            'form' => $form->createView(),
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
            if ($this->session->get('user'))
            {
                $user = $this->session->get('user');
            }
        }
        else
        {
            $user = $repositoryU->findOneBy(['login' => $login]);
        }

        if (!$user)
        {
            throw $this->createNotFoundException(sprintf('No user found for login : %s', $login));
        }

        return $this->render('home/user.html.twig', [
            'user' => $user,
        ]);
    }
}
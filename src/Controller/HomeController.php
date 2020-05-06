<?php


namespace App\Controller;


use App\Entity\Candidature;
use App\Entity\Commandes;
use App\Entity\Grades;
use App\Entity\Sections;
use App\Entity\Utilisateurs;
use App\Form\UtilisateurFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function homepage(EntityManagerInterface $em)
    {
        return $this->render('home/home.html.twig', [
            'sections' => $em->getRepository(Sections::class)->findAll(),
            'nbMember' => $em->getRepository(Utilisateurs::class)->countMembers(),
        ]);
    }

    /**
     * @Route("/connexion", name="app_connexion")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function connexion(EntityManagerInterface $em, Request $request)
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('app_user');
        }

        if ($request->isMethod('POST')) {
            $login = $request->get('login');
            $loginB = true;
            if ($login === "") {
                $loginB = false;
                $this->addFlash('fail', 'Merci de renseigner un login');
            }

            $password = $request->get('password');
            $passwordB = true;
            if ($password === "") {
                $passwordB = false;
                $this->addFlash('fail', 'Merci de renseigner le mot de passe');
            }

            if ($loginB && $passwordB) {
                $user = $em->getRepository(Utilisateurs::class)->findOneByLoginAndPassword($login, $password);
                if ($user) {
                    $this->session->set('user', $user);
                    $this->addFlash('success', 'Vous êtes connecté(e) !');
                    return $this->redirectToRoute('app_user');
                } else {
                    $this->addFlash('fail', 'Les identifiants sont incorrects');
                }
            }
        }

        return $this->render('home/connexion.html.twig');
    }

    /**
     * @Route("/deconnexion", name="app_deconnexion")
     */
    public function deconnexion()
    {
        if ($this->session->get('user')) {
            $this->session->clear();
        }

        return $this->redirectToRoute('app_homepage');
    }

    /**
     * @Route("/rejoindre/", name="app_rejoindre")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function rejoindre(EntityManagerInterface $em, Request $request)
    {
        if ($this->session->get('user')) {
            return $this->redirectToRoute('app_user');
        }

        if ($request->isMethod('POST')) {
            $login = $request->get('login');
            $loginB = true;
            if ($login === "") {
                $loginB = false;
                $this->addFlash('fail', 'Merci de renseigner un login');
            }

            $password = $request->get('password');
            $passwordB = true;
            if ($password === "") {
                $passwordB = false;
                $this->addFlash('fail', 'Merci de renseigner un mot de passe');
            }

            if ($loginB && $passwordB) {
                $candidature = new Candidature();
                $candidature
                    ->setLogin($login)
                    ->setMotDePasse($password)
                    ->setPrenom($request->get('prenom'))
                    ->setNom($request->get('nom'))
                    ->setSection($em->getRepository(Sections::class)->find($request->get('section')))
                    ->setMotivation($request->get('motivation'));

                $em->persist($candidature);
                $em->flush();

                $this->addFlash('success', 'Candidature envoyée');
                return $this->redirectToRoute('app_homepage');
            }
        }

        return $this->render('home/rejoindre.html.twig', [
            'sections' => $em->getRepository(Sections::class)->findAll(),
        ]);
    }

    /**
     * @Route("/user/{login}", name="app_user", defaults={"login"=""})
     * @param $login
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function user($login, EntityManagerInterface $em, Request $request)
    {
        if ($this->session->get('user')) {
            $form = $this->createForm(UtilisateurFormType::class,
                $em->getRepository(Utilisateurs::class)
                    ->findOneByLogin($this->session->get('user')->getLogin()));
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $form->getData();
                if ($user->getSection()) {
                    $user->setSection(
                        $em->getRepository(Sections::class)
                            ->findOneByNom(
                                $user->getSection()->getNom()
                            )
                    );
                    $user->setGrade(
                        $em->getRepository(Grades::class)
                            ->find(
                                $user->getGrade()->getId()
                            )
                    );
                }

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Vos données on été modifiée');
                return $this->redirectToRoute('app_user');
            }
        }

        if ($login === "") {
            if ($this->session->get('user')) {
                $user = $this->session->get('user');
            } else {
                return $this->redirectToRoute('app_homepage');
            }
        } else {
            $user = $em->getRepository(Utilisateurs::class)->findOneByLogin($login);
        }

        if (!$user) {
            throw $this->createNotFoundException(sprintf('No user found for login : %s', $login));
        }

        return $this->render('home/user.html.twig', [
            'user' => $user,
            'form' => $this->session->get('user') ? $form->createView() : null,
            'commandes' => $this->session->get('user') ? $em->getRepository(Commandes::class)->findAllByUser($this->session->get('user')) : [],
        ]);
    }
}
<?php


namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Grades;
use App\Entity\Sections;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moderation")
 */
class ModerationController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/{onglet}", name="app_moderation", defaults={"onglet"="utilisateurs"})
     * @param $onglet
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function moderation($onglet, EntityManagerInterface $em)
    {
        $user = $this->session->get('user');
        if ($user) {
            if ($user->getLogin() === "gashmob") {
                $utilisateurs = $em->getRepository(Utilisateurs::class)->findAll();
                $sections = $em->getRepository(Sections::class)->findAll();
                $candidatures = $em->getRepository(Candidature::class)->findAllNonAccepted();
                $effectifs = [];
                foreach ($sections as $section) {
                    $effectifs[$section->getNom()] = count($em->getRepository(Sections::class)
                        ->findAllMembers($section->getNom()));
                }
                $grades = $em->getRepository(Grades::class)->findAll();

                return $this->render('home/moderation.html.twig', [
                    'utilisateurs' => $utilisateurs,
                    'sections' => $sections,
                    'effectifs' => $effectifs,
                    'candidatures' => $candidatures,
                    'onglet' => $onglet,
                    'grades' => $grades,
                ]);
            } else {
                return $this->redirectToRoute('app_homepage');
            }
        } else {
            return $this->redirectToRoute('app_homepage');
        }
    }

    /**
     * @Route("/accept/{id}", name="app_candidature_accept")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function acceptCandidature($id, EntityManagerInterface $em)
    {
        $user = $this->session->get('user');
        if ($user) {
            if ($user->getLogin() === "gashmob") {
                if ($em->getRepository(Candidature::class)->accept($id)) {
                    $this->addFlash('success', 'La candidature a été acceptée, l\'utilisateur a été créé');
                } else {
                    $this->addFlash('fail', 'La candidature n\'a pas pu être acceptée');
                }
            }
        }

        return $this->redirectToRoute('app_moderation', ['onglet' => 'candidatures']);
    }

    /**
     * @Route("/rejecet/{id}", name="app_candidature_reject")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function rejectCandidature($id, EntityManagerInterface $em)
    {
        $user = $this->session->get('user');
        if ($user) {
            if ($user->getLogin() === "gashmob") {
                if ($em->getRepository(Candidature::class)->reject($id)) {
                    $this->addFlash('success', 'La candidature a été supprimée');
                } else {
                    $this->addFlash('fail', 'La candidature n\'a pas pu être supprimée');
                }
            }
        }

        return $this->redirectToRoute('app_moderation', ['onglet' => 'candidatures']);
    }

    /**
     * @Route("/modify/user", name="app_modify_user")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     * @throws NonUniqueResultException
     */
    public function modifyUser(Request $request, EntityManagerInterface $em)
    {
        if ($request->isMethod('POST')) {
            $login = $request->get('login');
            $prenom = $request->get('prenom');
            $nom = $request->get('nom');
            $section = $em->getRepository(Sections::class)->findOneByNom($request->get('section'));
            $grade = $em->getRepository(Grades::class)->find($request->get('grade'));

            $user = $em->getRepository(Utilisateurs::class)->findOneByLogin($login);
            $user->setLogin($login)
                ->setPrenom($prenom)
                ->setNom($nom)
                ->setSection($section)
                ->setGrade($grade);

            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('app_moderation', ['onglet' => 'utilisateurs']);
    }
}
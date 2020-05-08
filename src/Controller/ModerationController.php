<?php


namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Commandes;
use App\Entity\Grades;
use App\Entity\Produits;
use App\Entity\Sections;
use App\Entity\Types;
use App\Entity\Utilisateurs;
use App\ModerationAccess;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
            if (ModerationAccess::haveAccess($user)) {
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
                    'nbMembre' => $em->getRepository(Utilisateurs::class)->countMembers(),
                    'nbVisiteur' => $em->getRepository(Utilisateurs::class)->countVisitor(),
                    'sections' => $sections,
                    'effectifs' => $effectifs,
                    'candidatures' => $candidatures,
                    'onglet' => $onglet,
                    'grades' => $grades,
                    'commandes' => $em->getRepository(Commandes::class)->findAll(),
                    'produits' => $em->getRepository(Produits::class)->findAll(),
                    'types' => $em->getRepository(Types::class)->findAll(),
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
     * @param Request $request
     * @return RedirectResponse
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function acceptCandidature($id, EntityManagerInterface $em, Request $request)
    {
        $user = $this->session->get('user');
        if ($user) {
            if (ModerationAccess::haveAccess($user) && $request->isMethod('POST')) {
                if ($em->getRepository(Candidature::class)->accept($id, $request->get('grade'))) {
                    $this->addFlash('success', 'La candidature a été acceptée, l\'utilisateur a été créé');
                } else {
                    $this->addFlash('fail', 'La candidature n\'a pas pu être acceptée');
                }
            }
        }

        return $this->redirectToRoute('app_moderation', ['onglet' => 'candidatures']);
    }

    /**
     * @Route("/reject/{id}", name="app_candidature_reject")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function rejectCandidature($id, EntityManagerInterface $em)
    {
        $user = $this->session->get('user');
        if ($user) {
            if (ModerationAccess::haveAccess($user)) {
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

    /**
     * @Route("/delete/commande/{id}", name="app_delete_commande")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function deleteCommande($id, EntityManagerInterface $em)
    {
        $user = $this->session->get('user');
        if ($user) {
            if (ModerationAccess::haveAccess($user)) {
                if ($em->getRepository(Commandes::class)->delete($id)) {
                    $this->addFlash('success', 'La commande a été supprimée');
                } else {
                    $this->addFlash('fail', 'La commande n\'a pas pu être supprimée');
                }
            }
        }
        return $this->redirectToRoute('app_moderation', ['onglet' => 'commandes']);
    }

    /**
     * @Route("/modify/produit/{id}", name="app_modify_produit")
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function modifyProduit($id, Request $request, EntityManagerInterface $em)
    {
        if ($request->isMethod('POST')) {
            $nom = $request->get('nom');
            $prix = $request->get('prix');
            $description = $request->get('description');
            $image = $request->get('image');
            $type = $em->getRepository(Types::class)->find($request->get('type'));

            $produit = $em->getRepository(Produits::class)->find($id);
            $produit->setNom($nom)
                ->setPrix(floatval($prix))
                ->setDescription($description)
                ->setImage($image)
                ->setType($type);

            $em->persist($produit);
            $em->flush();
        }

        return $this->redirectToRoute('app_moderation', ['onglet' => 'produits']);
    }

    /**
     * @Route("/delete/produit/{id}", name="app_delete_produit")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function deleteProduit($id, EntityManagerInterface $em)
    {
        $user = $this->session->get('user');
        if ($user) {
            if (ModerationAccess::haveAccess($user)) {
                $em->getRepository(Produits::class)->delete($id);
            }
        }
        return $this->redirectToRoute('app_moderation', ['onglet' => 'produits']);
    }
}
<?php


namespace App\Controller;


use App\Entity\Produits;
use App\Entity\Utilisateurs;
use App\Form\CommandeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produits")
 */
class ProduitsController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/{page}", name="app_produits", defaults={"page"="1"})
     * @param $page
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function market($page, EntityManagerInterface $em)
    {
        return $this->render('produits/produits.html.twig', [
            'vaisseaux' => $em->getRepository(Produits::class)->findByTypeWithPagination('vaisseau', $page - 1),
            'nbVaisseaux' => $em->getRepository(Produits::class)->countByType('vaisseau'),
            'page' => $page
        ]);
    }

    /**
     * @Route("/vessel/{id}", name="app_show_vessel")
     * @param $id
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function showVessel($id, EntityManagerInterface $em, Request $request)
    {
        $repositoryP = $em->getRepository(Produits::class);
        $vaisseau = $repositoryP->find($id);

        $form = $this->createForm(CommandeFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commande = $form->getData();

            if ($commande->getQuantite() > 0) {
                $commande->setProduit($vaisseau);
                $commande->setUtilisateur($em->getRepository(Utilisateurs::class)->findOneByLogin($this->session->get('user')->getLogin()));

                $em->persist($commande);
                $em->flush();

                $this->addFlash('success', sprintf('Vous en avez commandé %d', $commande->getQuantite()));

                return $this->redirectToRoute('app_produits');
            }
        }

        if (!$vaisseau) {
            throw $this->createNotFoundException(sprintf('No vessel found for id : %s', $id));
        }

        return $this->render('produits/show_vessel.html.twig', [
            'vaisseau' => $vaisseau,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search/{keyword}", name="app_search", defaults={"keyword"=""})
     * @param $keyword
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function search($keyword, EntityManagerInterface $em)
    {
        return $this->render('produits/search.html.twig', [
            'keyword' => $keyword,
            'result' => $em->getRepository(Produits::class)->findLikeInType($keyword),
        ]);
    }
}
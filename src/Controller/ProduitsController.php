<?php


namespace App\Controller;


use App\Entity\Commandes;
use App\Entity\Produits;
use App\Entity\Types;
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
     * @Route("/", name="app_produits")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function market(EntityManagerInterface $em)
    {
        return $this->render('produits/produits.html.twig', [
            'vaisseaux' => $em->getRepository(Produits::class)->findByType('vaisseau'),
            'programmes' => $em->getRepository(Produits::class)->findByType('programme'),
            'mercenaires' => $em->getRepository(Produits::class)->findByType('mercenaires'),
        ]);
    }

    /**
     * @Route("/show/{id}", name="app_show_product")
     * @param $id
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function showVessel($id, EntityManagerInterface $em, Request $request)
    {
        $produits = $em->getRepository(Produits::class)->find($id);

        if (!$produits) {
            throw $this->createNotFoundException(sprintf('No product found for id : %s', $id));
        }

        if ($request->isMethod('POST')) {
            $quantite = $request->get('quantite');

            if ($quantite < 1) {
                $this->addFlash('fail', 'Merci de saisir une quantité supérieure à 0');
            } else {
                $commande = new Commandes();
                $commande
                    ->setProduit($produits)
                    ->setUtilisateur($em->getRepository(Utilisateurs::class)->findOneByLogin($this->session->get('user')->getLogin()))
                    ->setQuantite($quantite);

                $em->persist($commande);
                $em->flush();
                $this->addFlash('success', sprintf('Vous en avez commandé %d', $commande->getQuantite()));
            }
        }

        return $this->render('produits/show_product.html.twig', [
            'vaisseau' => $produits,
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
            'result' => $em->getRepository(Produits::class)->findLike($keyword),
        ]);
    }

    /**
     * @Route("/create/commande", name="app_create_commande")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @throws NonUniqueResultException
     */
    public function createCommande(Request $request, EntityManagerInterface $em)
    {
        if ($this->session->get('user')) {
            if ($request->isMethod('POST')) {
                $nom = $request->get('nom');
                $description = $request->get('description');
                $quantite = $request->get('quantite');

                $produit = new Produits();
                $produit->setType($em->getRepository(Types::class)->findOneByNom('commande'))
                    ->setNom($nom)
                    ->setPrix(0)
                    ->setDescription($description);
                $em->persist($produit);
                $em->flush();

                $commande = new Commandes();
                $commande->setProduit($produit)
                    ->setQuantite($quantite)
                    ->setUtilisateur($em->getRepository(Utilisateurs::class)->findOneByLogin($this->session->get('user')->getLogin()));
                $em->persist($commande);
                $em->flush();

                $this->addFlash('success', 'Votre commande a bien été créée');
                return $this->redirectToRoute('app_produits');
            }

            return $this->render('produits/create_commande.html.twig');
        }

        return $this->redirectToRoute('app_produits');
    }
}
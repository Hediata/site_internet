<?php


namespace App\Controller;


use App\Entity\Commandes;
use App\Entity\Sections;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sections")
 */
class SectionsController extends AbstractController
{
    /**
     * @Route("/{section}", name="app_sections", defaults={"section"=""})
     * @param $section
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function show_section($section, EntityManagerInterface $em)
    {
        if ($section === "")
        {
            $sections = $em->getRepository(Sections::class)->findAll();
            $effectifs = [];
            foreach ($sections as $sec)
            {
                $effectifs[$sec->getNom()] = count($em->getRepository(Sections::class)
                    ->findAllMembers($sec->getNom()));
            }

            return $this->render('sections/sections.html.twig', [
                'sections' => $sections,
                'effectifs' => $effectifs,
            ]);
        }
        else
        {
            $sec = $em->getRepository(Sections::class)->findOneByNom($section);
            switch ($section)
            {
                case 'R&D':
                    return $this->render('sections/rd.html.twig', [
                        'section' => $sec,
                        'commandes' => $em->getRepository(Commandes::class)->findAllProgrammeSortByDate(),
                        'membres' => $em->getRepository(Sections::class)->findAllMembers('R&D'),
                    ]);
                    break;

                case 'Industry':
                    return $this->render('sections/industry.html.twig', [
                        'section' => $sec,
                        'commandes' => $em->getRepository(Commandes::class)->findAllVesselSortByDate(),
                        'membres' => $em->getRepository(Sections::class)->findAllMembers('Industry'),
                    ]);
                    break;

                case 'Military':
                    return $this->render('sections/military.html.twig', [
                        'section' => $sec,
                        'commandes' => $em->getRepository(Commandes::class)->findAllMercenaireSortByDate(),
                        'membres' => $em->getRepository(Sections::class)->findAllMembers('Military'),
                    ]);
                    break;

                default:
                    throw $this->createNotFoundException(sprintf('No section found for name : %s', $section));
            }
        }
    }
}
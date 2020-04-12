<?php


namespace App\Controller;


use App\Entity\Sections;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sections")
 */
class SectionsController extends AbstractController
{
    /**
     * @Route("/{section}", name="app_sections", defaults={"section"=""})
     */
    public function show_section($section, EntityManagerInterface $em)
    {
        $repositoryS = $em->getRepository(Sections::class);

        if ($section === "")
        {
            return $this->render('sections/sections.html.twig', [
                'sections' => $repositoryS->findAll(),
            ]);
        }
        else
        {
            $sec = $repositoryS->findOneBy(['nom' => $section]);
            switch ($section)
            {
                case 'R&D':
                    return $this->render('sections/rd.html.twig', [
                       'section' => $sec,
                    ]);
                    break;

                case 'Industry':
                    return $this->render('sections/industry.html.twig', [
                       'section' => $sec,
                    ]);
                    break;

                case 'Military':
                    return $this->render('sections/military.html.twig', [
                        'section' => $sec,
                    ]);
                    break;

                default:
                    throw $this->createNotFoundException(sprintf('No section found for name : %s', $section));
            }
        }
    }
}
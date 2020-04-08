<?php


namespace App\Controller;


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
    public function show_section($section)
    {
        $sections = [
            [
                'nom' => 'R&D',
                'nb_membre' => 3,
                'description' => 'Description de la section'
            ],
            [
                'nom' => 'Industry',
                'nb_membre' => 4,
                'description' => 'Description de la section'
            ],
            [
                'nom' => 'Military',
                'nb_membre' => 4,
                'description' => 'Description de la section'
            ],
        ];

        switch ($section)
        {
            case 'R&D':
                return $this->render('sections/rd.html.twig', [
                    'section' => $sections[0],
                ]);
                break;

            case 'Industry':
                return $this->render('sections/industry.html.twig', [
                    'section' => $sections[1],
                ]);
                break;

            case 'Military':
                return $this->render('sections/military.html.twig', [
                    'section' => $sections[2],
                ]);
                break;

            default:
                return $this->render('sections/sections.html.twig', [
                    'sections' => $sections,
                ]);
        }
    }
}
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild", name="wild_")
 */

class WildController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }

    /**
     * @Route("/show/{slug}", requirements={"slug" = "^[a-z0-9]+(?:-[a-z0-9]+)*$"}, name="show")
     */
    public function show($slug = ''): Response
    {
        if(!empty($slug)) {
        $slug = str_replace('-', ' ', $slug);
        $slug = ucwords($slug);
        } else {
            $slug = 'Aucune série sélectionnée, veuillez choisir une série';
        }
        return $this->render('wild/show.html.twig', [
            'slug' => $slug,
        ]);
    }
}
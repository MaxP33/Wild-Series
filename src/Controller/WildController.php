<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
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
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        if (!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table.');
        }
        return $this->render('wild/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    /**
     * @Route("/show/{program}", name="show")
     * @param Program $program
     * @return Response
     */
    public function show(Program $program): Response
    {
        $seasons = $program->getSeasons();
        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    /**
     * @Route("/category/{categoryName}", requirements={"categoryName" = "^[a-z0-9]+(?:-[a-z0-9]+)*$"}, name="show_category")
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName): Response
    {
        if (!$categoryName) {
            throw $this->createNotFoundException('No category has been sent to find a category in category\'s table.');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($categoryName)]);
        if (!$category) {
            throw $this->createNotFoundException('No category' . $categoryName . 'found in category\'s table.');
        }
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category], ['id' =>  'desc'], 3);

        if (!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table.');
        }

        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
            'category' => $category,
        ]);
    }

    /**
     * @Route("/program/{slug}", requirements={"slug" = "^[a-z0-9]+(?:-[a-z0-9]+)*$"}, name="program")
     * @param string $slug
     * @return Response
     */
    public function showByProgram($slug = ''): Response
    {
        if (!$slug) {
            throw $this->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException( 'No program with '.$slug.' title, found in program\'s table.');
        }

        $seasons = $program->getSeasons();

        return $this->render('wild/program.html.twig', [
            'slug'    => $slug,
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    /**
     * @Route("/program/season/{id}", name="program_season")
     * @param int $id
     * @return Response
     */
    public function showBySeason(int $id): Response
    {
        if (!$id) {
            throw $this->createNotFoundException('No id has been sent to find a season in season\'s table.');
        }

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);

        $program = $season->getProgram();
        $episodes = $season->getEpisodes();


        return $this->render('wild/season.html.twig', [
            'season'   => $season,
            'program'  => $program,
            'episodes' => $episodes,
        ]);
    }

    /**
     * @Route("/program/{program}/season/{season}/episode/{episode}", name="program_season_episode")
     * @param Episode $episode
     * @param Season $season
     * @return Response
     */
    public function showByEpisode(Episode $episode, Season $season): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig', [
            'season'   => $season,
            'program'  => $program,
            'episode' => $episode,
        ]);
    }
}
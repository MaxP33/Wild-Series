<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\ProgramSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild", name="wild_")
 */

class WildController extends AbstractController
{
    /**
     * @Route("", name="index")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        if (!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table.');
        }

        $form = $this->createForm(ProgramSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();
            $programs = $em->getRepository(Program::class)
                ->findBy(['title' => $data['searchField']]);
        }

        return $this->render('wild/index.html.twig', [
            'programs'     => $programs,
            'form'         => $form->createview(),
        ]);
    }

    /**
     * @Route("/show/{slug}", name="show")
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
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function showByEpisode(Episode $episode, Request $request, EntityManagerInterface $em): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        $comments = $episode->getComments();

        $comment = new Comment();
        $form  = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $author = $this->getUser();
            $comment->setEpisode($episode);
            $comment->setAuthor($author);
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('wild_program_season_episode', ['program' => $program->getId(), 'season' => $season->getId(), 'episode' => $episode->getId()]);
        }
        return $this->render('wild/episode.html.twig', [
            'season'   => $season,
            'program'  => $program,
            'episode'  => $episode,
            'comments' => $comments,
            'form'     => $form->createView(),
        ]);
    }

    /**
     * @Route("/actor/{name}", name="actor")
     * @param Actor $actor
     * @return Response
     */
    public function showActor(Actor $actor): Response
    {
        $programs = $actor->getPrograms();
        return $this->render('wild/actor.html.twig', [
            'actor' => $actor,
            'programs' => $programs,
        ]);
    }
}
<?php


namespace App\Controller;


use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("", name="add")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $em->persist($category);
        $message = "";
        if($form->isSubmitted()) {
            $em->flush();
            $message = "Votre catégorie a été ajoutée";
        }

        return $this->render('category/add.html.twig', [
            'form'    => $form->createView(),
            'message' => $message,
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use MongoDB\Driver\Manager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index(Request $request)
    {

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        $formCat = $this->createForm(CategoryType::class);
        $formCat->handleRequest($request);
        $data = $formCat->getData();

        if ($formCat->isSubmitted()) {
            $this->add($data->getName());
        }
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories,
            'form' => $formCat->createView()
        ]);
    }

    public function add($name) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $category = new Category();
        $category->setName($name);

        $entityManager->persist($category);

        // execute SQL query
        // for this object but also all doctrine objects of the script
        $entityManager->flush();

        return $this->redirectToRoute('category');
    }
}

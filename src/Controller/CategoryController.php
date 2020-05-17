<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//Importamos las entidades
use App\Entity\CategoryTb;
use App\Entity\ProductTb;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function category(CategoryTb $category = NULL)
    {
        if ($category == NULL || !$category){
          return $this->RedirectToRoute('not-found',[
            "message" => "La página del evento no existe."
          ]);
        }

        $product_repo = $this->getDoctrine()->getRepository(ProductTb::class);
        $products = $product_repo->findBy(['id' => $category]);

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'category' => $category,
            'products' => $products
        ]);
    }
}

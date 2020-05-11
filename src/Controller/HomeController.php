<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//Importamos las entidades
use App\Entity\CategoryTb;

class HomeController extends AbstractController
{
    public function index(){
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    public function navbar($currentPath){

        $category_repo = $this->getDoctrine()->getRepository(CategoryTb::class);
        $search = $category_repo->findAll();

        $categories=[]; $subcategories=[];

        foreach ($search as $category) {
            if ($category->getParent() == NULL) {
                $categories[] = $category;
            } else {
                $subcategories[] = $category;
            }
        }

        foreach ($subcategories as $subcategory) {
          foreach ($categories as $category) {
            if ($subcategory->getParent() == $category){
                $category->addChild($subcategory);
            }
          }
        }

        return $this->render('_includes/blocks/navbar.html.twig', [
            'categories' => $categories,
            'subcategories' => $subcategories,
            'currentPath' => $currentPath
        ]);
    }
}

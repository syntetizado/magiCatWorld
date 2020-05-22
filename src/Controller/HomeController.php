<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
            if ($category->getParentSlug() == NULL) {
                $categories[] = $category;
            } else {
                $subcategories[] = $category;
            }
        }

        foreach ($subcategories as $subcategory) {
          foreach ($categories as $category) {
            if ($subcategory->getParentSlug() == $category->getSlug()){
                $category->addChild($subcategory->getSlug());
            }
          }
        }

        return $this->render('_includes/blocks/navbar.html.twig', [
            'categories' => $categories,
            'subcategories' => $subcategories,
            'currentPath' => $currentPath
        ]);
    }

    public function notFound(){
        return $this->render('home/error.html.twig', [
            'message' => 'La ruta especificada no existe'
        ]);
    }

    public function goBack(Request $request){

        $requestURI=$request->get('currentRoute');

        $after_bar = strrchr($requestURI, '/');
        $url = str_replace($after_bar,'',$requestURI);

        if ($url== '') {
            $url = "/";
        }

        return $this->redirect($url);
    }

    public function backToIndex(){
        return $this->RedirectToRoute('index');
    }

    public function loginSuccess(){
        return $this->render('home/index.html.twig', [
            'loginTrue' => true
        ]);
    }

    public function preLogout(){
        return $this->render('home/index.html.twig', [
            'logout' => true
        ]);
    }
}

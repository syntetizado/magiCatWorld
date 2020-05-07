<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//Importamos las entidades
use App\Entity\CategoryTb;

//Importamos los tipos de campo de formularios que necesitamos
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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

    public function loginPopup(){
      $form = $this->createFormBuilder()
						->add('nick', TextType::class,['label' => 'Nombre de usuario:'])
            ->add('password', PasswordType::class,['label' => 'Nombre de usuario:'])
          				->getForm();
      $icon = "<i class='fas fa-user'></i>";

      return $this->render('_includes/blocks/loginPopup.html.twig', [
        'controller_name' => 'HomeController',
        'modalTitle' => "Iniciar Sesión",
        'form' => $form->createView(),
        'icon' => $icon
      ]);
    }

    public function registerPopup(){
      $form = $this->createFormBuilder()
            ->add('nick', TextType::class,['label' => 'Nombre de usuario:'])
            ->add('password', PasswordType::class,['label' => 'Nombre de usuario:'])
                  ->getForm();
      $icon = '<i class="fas fa-address-book"></i>';

      return $this->render('_includes/blocks/registerPopup.html.twig', [
        'controller_name' => 'HomeController',
        'modalTitle' => "Registrarte",
        'form' => $form->createView(),
        'icon' => $icon
      ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

//Importamos las entidades
use App\Entity\CategoryTb;
use App\Entity\ContactformTb;

//Y los tipos de input de formularios necesarios
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            if ($category->getParentSlg() == NULL) {
                $categories[] = $category;
            } else {
                $subcategories[] = $category;
            }
        }

        foreach ($subcategories as $subcategory) {
          foreach ($categories as $category) {
            if ($subcategory->getParentSlg() == $category->getSlug()){
                $category->addChild(['slug' => $subcategory->getSlug(),'name'=>$subcategory->getName()]);
            }
          }
        }
        $categories2=$categories;
        return $this->render('_includes/blocks/navbar.html.twig', [
            'categories' => $categories,
            'categories2' => $categories2,
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

    public function sendForm(Request $request){

        $contact = new ContactformTb();

        $form = $this->createFormBuilder()
			->add('email', TextType::class,['required' => false])
            ->add('name', TextType::class,['required' => false])
            ->add('topic', TextType::class,['required' => false])
            ->add('description', TextareaType::class,['required' => false])
        ->getForm();

        // Comprobamos la solicitud
		$form->handleRequest($request);

		// Comrpobamos si el formuario se ha registrado y es valido
		if ($form->isSubmitted() && $form->isValid()){

            if ($form->get('name')->getData() != NULL) {
                $contact->setName($form->get('name')->getData());
            }
            if ($form->get('email')->getData() != NULL) {
                $contact->setEmail($form->get('email')->getData());
            }
            if ($form->get('topic')->getData() != NULL) {
                $contact->setTopic($form->get('topic')->getData());
            }
            if ($form->get('description')->getData() != NULL) {
                $contact->setDescription($form->get('description')->getData());
            }

            $date = date('Y-m-d H:i:s');
            $contact->setDate(new \DateTime());



            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return $this->render('home/contactForm.html.twig', [
                'form' => $form->createView(),
                'message' => "Enviado correctamente",
                'icon' => '<i class="fas fa-check text-success"></i>'
            ]);
        } else {
            return $this->render('home/contactForm.html.twig', [
                'form' => $form->createView(),
                'message' => '',
                'icon' => ''
            ]);
        }
    }
}

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
    //Renderiza la página principal
    public function index(){
        return $this->render('home/index.html.twig');
    }

    //Renderiza la parte del header donde se encuentra el navBar
    public function navbar($currentPath){

        //Primero recoge todas las categorias de la BD
        $category_repo = $this->getDoctrine()->getRepository(CategoryTb::class);
        $search = $category_repo->findAll();

        //inicia las variables
        $categories=[]; $subcategories=[];

        //Aquí, busca las categorías que son o no principales
        foreach ($search as $category) {
            //Si no tiene slug en parent slug, es principal
            if ($category->getParentSlg() == NULL) {
                $categories[] = $category;
            } else {
                $subcategories[] = $category;
            }
        }

        //Agrega las subcategorias a las principales antes de mandar a plantilla
        foreach ($subcategories as $subcategory) {
          foreach ($categories as $category) {
            if ($subcategory->getParentSlg() == $category->getSlug()){
                $category->addChild(['slug' => $subcategory->getSlug(),'name'=>$subcategory->getName()]);
            }
          }
        }

        //Un simple arreglo para poder hacer 2 for en twig
        $categories2=$categories;

        return $this->render('_includes/blocks/navbar.html.twig', [
            'categories' => $categories,
            'categories2' => $categories2,
            'subcategories' => $subcategories,
            'currentPath' => $currentPath
        ]);
    }

    //si no existe la ruta, devolverá esta ruta y mostrará un error
    public function notFound(){
        return $this->render('home/error.html.twig', [
            'message' => 'La ruta especificada no existe'
        ]);
    }

    //Es una función utilizada para ir atras una vez en la URL
    public function goBack(Request $request){

        $requestURI=$request->get('currentRoute');

        $after_bar = strrchr($requestURI, '/');
        //elimina la última barra de la ruta
        $url = str_replace($after_bar,'',$requestURI);

        if ($url== '') {
            $url = "/";
        }

        //Y realiza una redirección al sitio correcto
        return $this->redirect($url);
    }

    //Es una redirección para cuando es necesario volver al index
    public function backToIndex(){
        return $this->RedirectToRoute('index');
    }

    //Se utiliza para que aparezca un mensaje en index
    //de que se ha iniciado sesión correctamente
    public function loginSuccess(){
        return $this->render('home/index.html.twig', [
            'loginTrue' => true
        ]);
    }

    //Esta función se usa para preguntar al usuario si de verdad
    //quiere desconectar sesión
    public function preLogout(){
        return $this->render('home/index.html.twig', [
            'logout' => true
        ]);
    }

    //Se utiliza para generar el formulario de contacto
    public function sendForm(Request $request){

        //primero se crea el formulario
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

            //se crea el nuevo objeto de la BD
            $contact = new ContactformTb();

            //se le añaden todos los valores necesarios
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

            $contact->setDate(new \DateTime());//la fecha de hoy


            //Con estas 3 líneas introducimos los datos en la BD
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            //renderizamos el formulario pero con un mensaje
            return $this->render('home/contactForm.html.twig', [
                'form' => $form->createView(),
                'message' => "Enviado correctamente",
                'icon' => '<i class="fas fa-check text-success"></i>'
            ]);
        } else {
            //renderizamos el formulario solamente
            return $this->render('home/contactForm.html.twig', [
                'form' => $form->createView(),
                'message' => '',
                'icon' => ''
            ]);
        }
    }

    //Devuelve la plantilla donde aparece la información de empresa
    public function information() {
        return $this->render('home/information.html.twig');
    }
}

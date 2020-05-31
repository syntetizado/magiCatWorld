<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// Importamos las clases relativas a respuestas y peticiones HTTP
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

//cargamos entidades
use App\Entity\UserTb;

//Importamos los tipos de campo de formularios que necesitamos
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ModalController extends AbstractController
{
    /**
     * @Route("/modal", name="modal")
     */

    //Solo renderiza un modal con un formulario para loguear
    public function loginPopup(){
        //Creamos el formulario
        $form = $this->createFormBuilder()
            ->add('nick', TextType::class,['label' => 'Nombre de usuario:'])
            ->add('password', PasswordType::class,['label' => 'Nombre de usuario:'])
                ->getForm();

        //Y lo enviamos como un renderizado. Esto lo recibira javaScript
        //y lo añadira al html, para ser usado dinámicamente.
        return $this->render('modal/loginPopup.html.twig', [
            'controller_name' => 'ModalController',
            'modalTitle' => "Iniciar sesión",
            'form' => $form->createView(),
            'icon' => "<i class='fas fa-user'></i>"
        ]);
   	}

    //igual al anterior. Crea un modal, pero de registro de usuario
    public function registerPopup(){

        //creamos el formulario
        $form = $this->createFormBuilder()
            ->add('nick', TextType::class)
            ->add('email', TextType::class)
            ->add('password', PasswordType::class)
            ->add('repassword', PasswordType::class)
            ->add('name', TextType::class,['required' => false])
            ->add('surname1', TextType::class,['required' => false])
            ->add('surname2', TextType::class,['required' => false])
            ->add('direction', TextType::class,['required' => false])
            ->add('phone', TelType::class,['required' => false])
                ->getForm();

        //Y lo devolvemos junto con otras
        //variables personalizadas para la plantilla
        return $this->render('modal/registerPopup.html.twig', [
            'controller_name' => 'ModalController',
            'modalTitle' => "Registro de usuario",
            'form' => $form->createView(),
            'icon' => "<i class='fas fa-user'></i>",
            'message' => 'mensaje',
            'message_icon' => 'icono exito'
        ]);
    }

    //esto crea un modal que devuelve una información al usuario.
    //Temas de symfony impiden que esto se haga sin redirigir en el login.
    public function infoPopup($message, $modalTitle, $icon, $toggle, $activated){

        return $this->render('modal/infoPopup.html.twig', [
            'modalTitle' => $modalTitle,
            'icon' => $icon,
            'message' => $message,
            'toggle' => $toggle,
            'activated' => $activated
        ]);
    }

    //este es otro modal renderizado para que js lo use.
    public function userPopup(){

        return $this->render('modal/userPopup.html.twig', [
            'modalTitle' => 'Datos de usuario',
            'icon' => "<i class='fas fa-user'></i>",
        ]);
    }

    //este modal pedirá confirmación al usuario para desconectar la sesión
    public function loginStatusPopup($message, $modalTitle, $icon,$ask){

        return $this->render('modal/loginStatusPopup.html.twig', [
            'modalTitle' => $modalTitle,
            'icon' => $icon,
            'message' => $message,
            'ask' => $ask
        ]);
    }
}

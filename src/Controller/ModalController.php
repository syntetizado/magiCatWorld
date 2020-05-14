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
    public function loginPopup(){
        $form = $this->createFormBuilder()
            ->add('nick', TextType::class,['label' => 'Nombre de usuario:'])
            ->add('password', PasswordType::class,['label' => 'Nombre de usuario:'])
                ->getForm();

        return $this->render('modal/loginPopup.html.twig', [
            'controller_name' => 'ModalController',
            'modalTitle' => "Registro de usuario",
            'form' => $form->createView(),
            'icon' => "<i class='fas fa-user'></i>",
        ]);
   	}

    public function registerPopup(){
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

        return $this->render('modal/registerPopup.html.twig', [
            'controller_name' => 'ModalController',
            'modalTitle' => "Registro de usuario",
            'form' => $form->createView(),
            'icon' => "<i class='fas fa-user'></i>",
            'message' => 'mensaje',
            'message_icon' => 'icono exito'
        ]);
    }

    public function infoPopup($message, $modalTitle, $icon, $toggle){

        return $this->render('modal/infoPopup.html.twig', [
            'modalTitle' => $modalTitle,
            'icon' => $icon,
            'message' => $message,
            'toggle' => $toggle
        ]);
    }
}

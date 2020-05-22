<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//cargamos entidades
use App\Entity\UserTb;

//cargamos componentes necesarios
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

//Importamos los tipos de campo de formularios que necesitamos
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Symfony\Component\Validator\Constraints as Assert;


class AjaxController extends AbstractController
{
    /**
    * @Route("/ajax", name="ajax")
    */

    public function registerUser(UserPasswordEncoderInterface $encoder, Request $request){
        $response = new Response();

        // Ajax request
        if ($request->isXmlHttpRequest()) {
            $error = false;

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
            $form->submit($request->get('form'));

            $user_repo = $this->getDoctrine()->getRepository(UserTb::class);

            if ($form->isSubmitted() && $form->isValid()) {
                $pass = $form->get('password')->getData();
                $cPass = $form->get('repassword')->getData();

                if ($user_repo->findBy(['email' => $form->get('email')->getData()])){

                    $response = $this->forward('App\Controller\ModalController::infoPopup', [
                        'modalTitle' => 'Error',
                        'icon' => '<i class="fas fa-times text-danger"></i>',
                        'message' => 'El email ya existe',
                        'toggle' => 'register',
                        'activated' => 'no'
                    ]);
                    return $response;
                }

                if ($user_repo->findBy(['nick' => $form->get('nick')->getData()])){

                    $response = $this->forward('App\Controller\ModalController::infoPopup', [
                        'modalTitle' => 'Error',
                        'icon' => '<i class="fas fa-times text-danger"></i>',
                        'message' => 'El nick ya existe',
                        'toggle' => 'register',
                        'activated' => 'no'
                    ]);
                    return $response;
                }

                if ($pass != $cPass){
                    $response = $this->forward('App\Controller\ModalController::infoPopup', [
                        'modalTitle' => $form->get('nick')->getData(),
                        'icon' => '<i class="fas fa-times text-danger"></i>',
                        'message' => 'Las contraseñas no coinciden',
                        'toggle' => 'register',
                        'activated' => 'no'
                    ]);
                    $error = true;
                    return $response;
                }

                if ($error == false){
                    $user = new UserTb();
                    $user
                        ->setEmail($form->get('email')->getData())
                        ->setName($form->get('name')->getData())
                        ->setSurname1($form->get('surname1')->getData())
                        ->setSurname2($form->get('surname2')->getData())
                        ->setDirection($form->get('direction')->getData())
                        ->setNick($form->get('nick')->getData())
                        ->setPassword($form->get('password')->getData())
                        ->setRol('ROLE_USER')
                        ->setImage('user-default.png');

                    $encodedPw = $encoder->encodePassword($user, $user->getPassword());
                    $user->setPassword($encodedPw);

                    $entityManager= $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                }

                $response = $this->forward('App\Controller\ModalController::infoPopup', [
                    'modalTitle' => 'Usuario registrado con éxito',
                    'icon' => '<i class="fas fa-check text-success"></i>',
                    'message' => 'Ahora puedes iniciar sesión',
                    'toggle' => 'login',
                    'activated' => 'no'
                ]);
                return $response;
            } else {
                $response = $this->forward('App\Controller\ModalController::infoPopup', [
                    'modalTitle' => 'Error',
                    'icon' => '<i class="fas fa-times text-danger"></i>',
                    'message' => 'Error al enviar el formulario',
                    'toggle' => '',
                    'activated' => 'no'
                ]);
                return $response;
            }
        } else {
            if ($pass != $cPass){
                $response = $this->forward('App\Controller\ModalController::infoPopup', [
                    'modalTitle' => 'Error',
                    'icon' => '<i class="fas fa-times text-danger"></i>',
                    'message' => 'Error al enviar el formulario',
                    'toggle' => '',
                    'activated' => 'no'
                ]);
            return $response;
            }
        }
    }

    public function logUserSuccess(){

            $response = $this->forward('App\Controller\ModalController::loginStatusPopup', [
                'modalTitle' => 'Datos correctos!',
                'icon' => '<i class="fas fa-check text-success"></i>',
                'message' => 'Has iniciado sesión correctamente',
                'ask' => false
            ]);
            return $response;
    }

    public function logUserError(){

            $response = $this->forward('App\Controller\ModalController::loginStatusPopup', [
                'modalTitle' => 'Error',
                'icon' => '<i class="fas fa-times text-danger"></i>',
                'message' => 'Error al intentar iniciar sesión',
                'ask' => false
            ]);
            return $response;
    }

    public function logout(){

            $response = $this->forward('App\Controller\ModalController::loginStatusPopup', [
                'modalTitle' => 'Cierre de sesión',
                'icon' => '<i class="fas fa-exclamation-circle text-info"></i>',
                'message' => '¿Seguro que quieres cerrar sesión?',
                'ask' => true
            ]);
            return $response;
    }
}

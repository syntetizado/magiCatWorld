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

    //esta función se usa para crear un usuario en la BD
    public function registerUser(UserPasswordEncoderInterface $encoder, Request $request){

        $response = new Response();

        // Si existe un request ajax
        if ($request->isXmlHttpRequest()) {
            $error = false;

            //crea un formulario
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

            //y simula el enviado del formulario
            $form->submit($request->get('form'));

            $user_repo = $this->getDoctrine()->getRepository(UserTb::class);

            //de esta forma podemos comprobar los datos que nos llegan de fuera
            if ($form->isSubmitted() && $form->isValid()) {
                $pass = $form->get('password')->getData();
                $cPass = $form->get('repassword')->getData();

                //Tira error en el caso que exista un email igual al recogido.
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

                //Lo mismo si hay un usuario duplicado
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

                //También dará error si las contraseñas no son iguales
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

                //si no hay errores crea el usuario
                //(en realidad no es necesario comprobar,
                //pues se detendría la ejecución en el return)
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

                    //antes de pasar la contraseña, la codifica.
                    $encodedPw = $encoder->encodePassword($user, $user->getPassword());
                    $user->setPassword($encodedPw);

                    //Luego recoge el manager, y pasa a la base de Datos
                    //los datos del nuevo usuario
                    $entityManager= $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                }

                //Al registrarse correctamente, muestra un mensaje de éxito al renderizar
                $response = $this->forward('App\Controller\ModalController::infoPopup', [
                    'modalTitle' => 'Usuario registrado con éxito',
                    'icon' => '<i class="fas fa-check text-success"></i>',
                    'message' => 'Ahora puedes iniciar sesión',
                    'toggle' => 'login',
                    'activated' => 'no'
                ]);
                return $response;
            } else {
                //si no se ha enviado todavía, muestra el formulario
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
            //si no se ha recibido ningún request ajax, devuelve igualmente esto.
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

    //Devuelve un mensaje de éxito al iniciar sesión. Devuelve modal vía ajax
    public function logUserSuccess(){

            $response = $this->forward('App\Controller\ModalController::loginStatusPopup', [
                'modalTitle' => 'Datos correctos!',
                'icon' => '<i class="fas fa-check text-success"></i>',
                'message' => 'Has iniciado sesión correctamente',
                'ask' => false
            ]);
            return $response;
    }

    //Igual al anterior, pero devuelve el modal de error.
    public function logUserError(){

            $response = $this->forward('App\Controller\ModalController::loginStatusPopup', [
                'modalTitle' => 'Error',
                'icon' => '<i class="fas fa-times text-danger"></i>',
                'message' => 'Error al intentar iniciar sesión',
                'ask' => false
            ]);
            return $response;
    }

    //Devuelve una ventana en la que se pregunta si se quiere cerrar sesión,
    //esperando confirmación
    public function logout(){

            $response = $this->forward('App\Controller\ModalController::loginStatusPopup', [
                'modalTitle' => 'Cierre de sesión',
                'icon' => '<i class="fas fa-exclamation-circle text-info"></i>',
                'message' => '¿Seguro que quieres cerrar sesión?',
                'ask' => true
            ]);
            return $response;
    }

    //Crea un mensaje de información
    //con los datos que se le envía a través del request.
    //Pide 2 datos GET, "title" y "message"
    public function message(Request $request) {
        $response = $this->forward('App\Controller\ModalController::infoPopup', [
            'modalTitle' => $request->get('title'),
            'icon' => '<i class="fas fa-exclamation-circle text-info"></i>',
            'message' => $request->get('message'),
            'ask' => false,
            'toggle' => '',
            'activated' => 'yes'
        ]);
        return $response;
    }
}

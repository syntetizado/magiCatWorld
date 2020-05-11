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

        /*if (!$request->isXmlHttpRequest()) {
    return new JsonResponse(array(
        'status' => 'Error',
        'message' => 'Error'),
    400);
}*/
         // Ajax request
        if ($request->isXmlHttpRequest()) {

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

                    /*return $this->render('modal/messagePopupModal.html.twig', [
                        'controller_name' => 'ModalController',
                        'modalTitle' => "Registro de usuario",
                        'message' => 'Ya existe un usuario con este email.',
                        'message_icon' => 'EMAIL_EXISTS_ICON'
                    ];*/
                }

                if ($pass != $cPass){
                    /*return [
                        'controller_name' => 'ModalController',
                        'modalTitle' => "Iniciar Sesión",
                        'message' => 'Las contraseñas no son iguales'
                    ];*/
                    return new Response();
                }

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
                    ->setImage('user-default.jpg');

                $encodedPw = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encodedPw);

                $entityManager= $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                return new Response();
            } else {
                return new Response();
            }


        } else {
            return new Response();
        }
    }
}

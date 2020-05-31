<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//importamos la clase del usuario
use App\Entity\UserTb;

use App\Security\UserChecker;//ignorar. No se está usando.

//Importamos utilidades que tienen que ver con la seguridad en Symfony.
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;


class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */

    //esta es la ruta usada por Symfony para iniciar sesión.
    //Basta con introducir AuthenticationUtils como valor
    public function login(AuthenticationUtils $authenticationUtils,Security $security){

        //Da error si intentamos logearnos con datos erróneos
        if (!$security->getUser()){
            return $this->render('home/index.html.twig', [
                'loginError' => true
            ]);
        }

        //Si no es así, devuelve un mensaje de éxito
        return $this->RedirectToRoute('login-success');
    }
}

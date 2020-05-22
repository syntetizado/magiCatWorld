<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\UserTb;
use App\Security\UserChecker;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;


class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    public function login(AuthenticationUtils $authenticationUtils,Security $security){

        die(var_dump($security));

        if (!$security->getUser()){
            return $this->render('home/index.html.twig', [
                'loginError' => true
            ]);
        }

        return $this->RedirectToRoute('login-success');
    }
}

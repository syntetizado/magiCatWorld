<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//cargamos las entidades necesarias
use App\Entity\UserTb;
use App\Entity\ProductTb;
use App\Entity\ReviewTb;
use App\Entity\CategoryTb;
use App\Entity\ContactformTb;
use App\Entity\OrderTb;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    #######################################################
    ######################## USERS ########################
    #######################################################

    public function users(){
        $user_repo = $this->getDoctrine()->getRepository(UserTb::class);
        $users = $user_repo->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    public function activeUser(UserTb $user){

        if ($user->getActive())
            $user->setActive(0);
        else
            $user->setActive(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->RedirectToRoute('admin-users');

    }

    #######################################################
    ####################### PRODUCTS ######################
    #######################################################

    public function products(){
        $product_repo = $this->getDoctrine()->getRepository(ProductTb::class);
        $products = $product_repo->findAll();

        return $this->render('admin/products.html.twig', [
            'products' => $products
        ]);
    }

    public function activeProduct(ProductTb $product){

        if ($product->getActive())
            $product->setActive(0);
        else
            $product->setActive(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->RedirectToRoute('admin-products');
    }

    #######################################################
    ####################### REVIEWS #######################
    #######################################################

    public function reviews(){
        $review_repo = $this->getDoctrine()->getRepository(ReviewTb::class);
        $reviews = $review_repo->findAll();

        return $this->render('admin/reviews.html.twig', [
            'reviews' => $reviews
        ]);
    }

    public function banReview(ReviewTb $review){

        if ($review->getBanned())
            $review->setBanned(0);
        else
            $review->setBanned(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($review);
        $em->flush();

        return $this->RedirectToRoute('admin-reviews');
    }

    #######################################################
    ####################### ORDERS ########################
    #######################################################

    public function orders(){
        $order_repo = $this->getDoctrine()->getRepository(OrderTb::class);
        $orders = $order_repo->findAll();

        return $this->render('admin/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    public function activeOrder(OrderTb $order){

        if ($order->getActive())
            $order->setActive(0);
        else
            $order->setActive(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        return $this->RedirectToRoute('admin-orders');
    }

    #######################################################
    ####################### MESSAGES ######################
    #######################################################

    public function messages(){
        $message_repo = $this->getDoctrine()->getRepository(ContactformTb::class);
        $messages = $message_repo->findAll();

        return $this->render('admin/messages.html.twig', [
            'messages' => $messages
        ]);
    }

    public function hideMessage(ContactformTb $message){

        if ($message->getActive())
            $message->setActive(0);
        else
            $message->setActive(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return $this->RedirectToRoute('admin-messages');
    }

    public function deleteMessage(ContactformTb $message){

        $em = $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();

        return $this->RedirectToRoute('admin-messages');
    }

    #######################################################
    ####################### CATEGORIES ####################
    #######################################################
}

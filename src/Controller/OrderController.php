<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;

use App\Entity\ProductTb;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function order()
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    public function addToOrder(Request $request)
    {
        $quantity=$request->query->get('quantity');
        $product=$request->query->get('product-id');

        $product_repo=$this->getDoctrine()->getRepository(ProductTb::class);
        $product_result=$product_repo->findBy(['id' => $product]);
        foreach ($product_result as $product_item) {
            $product=$product_item;
        }


        return $this->render('order/index.html.twig', [
            'quantity' => $quantity,
            'product' => $product
        ]);
    }
}

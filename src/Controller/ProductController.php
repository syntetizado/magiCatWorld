<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//cargamos las entidades necesarias
use App\Entity\ProductTb;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function product(ProductTb $product = NULL){

        if (!$product || $product->getActive() == 0){
            return $this->render('home/error.html.twig', [
                'message' => "Este producto no existe",
                'number' => 404
            ]);
        }

        return $this->render('product/index.html.twig', [
            'product' => $product
        ]);
    }

    public function latestProducts(){

        $product_repo = $this->getDoctrine()->getRepository(ProductTb::class);
        $products = $product_repo->findAll();

        $numberOfProducts=10;

        if (count($products)<$numberOfProducts) {
            $products = $product_repo->findBy([],['id' => 'DESC'],count($products));
        } else {
            $products = $product_repo->findBy([],['id' => 'DESC'],$numberOfProducts);
        }


        return $this->render('_includes/blocks/products.html.twig', [
            'products' => $products
        ]);
    }

    public function productCardTrimDesc($product){

        $trimNumber=100;

        if ( strlen( $product->getDescription() ) > $trimNumber ){
            $trimmedDescription=substr($product->getDescription(), 0, $trimNumber);
        } else {
            $trimmedDescription=$product->getDescription();
        }

        return $this->render('_includes/blocks/product-card.html.twig', [
            'product' => $product,
            'trimmedDescription' => $trimmedDescription
        ]);
    }
}

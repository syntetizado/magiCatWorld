<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;

//cargamos las entidades necesarias
use App\Entity\ProductTb;
use App\Entity\ProductFeatureTb;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function product(ProductTb $product = NULL){

        if ($product == NULL || $product->getActive() == 0){
            return $this->render('home/error.html.twig', [
                'message' => "Este producto no existe",
                'number' => 404
            ]);
        }

        $productFeature_repo = $this->getDoctrine()->getRepository(ProductFeatureTb::class);
        $features = $productFeature_repo->findBy(['idProduct' => $product->getId()]);

        return $this->render('product/index.html.twig', [
            'product' => $product,
            'features' => $features
        ]);
    }

    public function latestProducts($nprods = NULL){

        if (!$nprods) {
            $nprods = 12;
        }

        $product_repo = $this->getDoctrine()->getRepository(ProductTb::class);
        $products = $product_repo->findBy([],['id' => 'DESC'],$nprods);

        foreach ($products as $product) {
            $productFeature_repo = $this->getDoctrine()->getRepository(ProductFeatureTb::class);
            $features = $productFeature_repo->findBy(['idProduct' => $product->getId()]);
            foreach ($features as $feature) {
                $product->addFeature($feature);
            }

        }

        return $this->render('_includes/blocks/products-latest.html.twig', [
            'products' => $products,
            'features' => ''

        ]);
    }

    public function productCard($product,$height = NULL){

        $trimNumber=100;

        if ( strlen( $product->getDescription() ) > $trimNumber ){
            $trimmedDescription=substr($product->getDescription(), 0, $trimNumber);
        } else {
            $trimmedDescription = NULL;
        }

        return $this->render('_includes/blocks/product-minicard.html.twig', [
            'product' => $product,
            'height' => $height,
            'trimmedDescription' => $trimmedDescription
        ]);
    }

    public function productSearch(Request $request){

        $search=$request->get('search');

        $product_repo = $this->getDoctrine()->getRepository(ProductTb::class);
        $em=$this->getDoctrine()->getManager();
        $products = $result = $em->getRepository(ProductTb::class)->createQueryBuilder('o')
            ->where('o.name LIKE :search')
            ->setParameter('search', "%".$search."%")
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
        if (!$products){
            $products = $result = $em->getRepository(ProductTb::class)->createQueryBuilder('o')
                ->where('o.description LIKE :search')
                ->setParameter('search', "%".$search."%")
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);
        }

        return $this->render('product/product-search.html.twig', [
            'products' => $products
        ]);
    }
}

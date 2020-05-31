<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;

//cargamos las entidades necesarias
use App\Entity\ProductTb;
use App\Entity\ReviewTb;
use App\Entity\ProductFeatureTb;

//cargamos los tipos de formularios necesarios
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

//Importamos el componente que tiene el usuario actual
use Symfony\Component\Security\Core\Security;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */

    //para testear variables. Muy útil
    public function varTest($var){
        die($this->render('test.html.twig',[
            'var' => $var
    ]));

    }//$this->varTest($orders);

    public function userHasReview($product,$security){
        if (!$security->getUser()) {
            return false;
        }
        $userId = $security->getUser()->getId();
        $review = $this ->getDoctrine()
                        ->getRepository(ReviewTb::class)
                        ->findOneBy(['idProduct'=>$product->getId(),'idUser'=>$userId]);


        if ($review) {
            return true;
        } else {
            return false;
        }
    }

    public function product(ProductTb $product = NULL, Request $request, Security $security){

        if ($product == NULL || $product->getActive() == 0){
            return $this->render('home/error.html.twig', [
                'message' => "Este producto no existe",
                'number' => 404
            ]);
        }

        $form = $this->createFormBuilder()
			->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('rating', ChoiceType::class,[
          'placeholder' => 'Puntua el producto',
          'choices' => [
              '★' => 1,
              '★★' => 2,
              '★★★' => 3,
              '★★★★' => 4,
              '★★★★★' => 5
          ]])
        ->getForm();

        // Comprobamos la solicitud
		$form->handleRequest($request);

        $exists = $this->userHasReview($product,$security);


		// Comrpobamos si el formuario se ha registrado y es valido
		if ($form->isSubmitted() && $form->isValid() && !$exists) {

            $review = new ReviewTb();


            if ($form->get('rating')->getData() != NULL) {
                $review->setRating($form->get('rating')->getData());
            }
            if ($form->get('title')->getData() != NULL) {

                $user = $security->getUser();

                $review->setTitle($form->get('title')->getData());
                $review->setIdUser($user);
                $review->setIdProduct($product);

                $slug = strtolower($user->getNick().'_'.$review->getTitle());
                $search = ['/', ',', '.','*',' ', 'á', 'é', 'í', 'ó', 'ú'];
                $replace = ['', '', '','','-', 'a', 'e', 'i', 'o', 'u'];
                $slug = str_replace($search, $replace, $slug);

                $review_repo = $this->getDoctrine()->getRepository(ReviewTb::class);
                if ($review_repo->findBy(['slug' => $slug])){
                    $slug=$slug."x";
                }
                $review->setSlug($slug);
            }

            if ($form->get('description')->getData() != NULL) {
                $review->setDescription($form->get('description')->getData());
            }

            $review->setDate(new \DateTime());

			$entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();
		}
        $productFeature_repo = $this->getDoctrine()->getRepository(ProductFeatureTb::class);
        $features = $productFeature_repo->findBy(['idProduct' => $product->getId()]);

        $review_repo = $this->getDoctrine()->getRepository(ReviewTb::class);
        $reviews = $review_repo->findBy(['idProduct' => $product->getId()]);

        return $this->render('product/index.html.twig', [
            'product' => $product,
            'features' => $features,
            'reviews' => $reviews,
            'form' => $form->createView(),
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

    public function productCard($product){

        return $this->render('_includes/blocks/product-minicard.html.twig', [
            'product' => $product
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
            'products' => $products,
            'search' => $search
        ]);
    }
}

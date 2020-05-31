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

    //devuelve verdadero o falso dependiendo de si el usuario logueado
    //tiene o no la opinión creada en un producto en concreto
    public function userHasReview($product,$security){

        //si no hay usuario, no comprueba más
        if (!$security->getUser()) {
            return false;
        }

        //Si lo hay, lo obtiene, y recoge de la BD si hay alguna review
        //de este producto en concreto
        $userId = $security->getUser()->getId();
        $review = $this ->getDoctrine()
                        ->getRepository(ReviewTb::class)
                        ->findOneBy(['idProduct'=>$product->getId(),'idUser'=>$userId]);

        //Dependiendo si existe o no, devuelve true o false
        if ($review) {
            return true;
        } else {
            return false;
        }
    }

    //Esta función devuelve la tarjeta principal descriptiva de un producto
    public function product(ProductTb $product = NULL, Request $request, Security $security){

        //Si no existe devuelve error 404
        if ($product == NULL || $product->getActive() == 0){
            return $this->render('home/error.html.twig', [
                'message' => "Este producto no existe",
                'number' => 404
            ]);
        }

        //Si existe, crea el formulario del review
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

        //Comprobamos que no tenga una review ya escrita
        $exists = $this->userHasReview($product,$security);


		// Comrpobamos si el formuario se ha registrado y es valido
		if ($form->isSubmitted() && $form->isValid() && !$exists) {

            $review = new ReviewTb();

            //Si los datos están correctos los añade al nuevo comentario
            if ($form->get('rating')->getData() != NULL) {
                $review->setRating($form->get('rating')->getData());
            }
            if ($form->get('title')->getData() != NULL) {

                $user = $security->getUser();

                $review->setTitle($form->get('title')->getData());
                $review->setIdUser($user);
                $review->setIdProduct($product);

                //crea un nombre controlado ante urls
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

            //Le añade la fecha de ahora mismo
            $review->setDate(new \DateTime());

            //Recogemos el manager de la base de Datos
            //y escribimos los datos en la BD
            $entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();
		}

        //recoge las características del producto si existen
        $productFeature_repo = $this->getDoctrine()->getRepository(ProductFeatureTb::class);
        $features = $productFeature_repo->findBy(['idProduct' => $product->getId()]);

        //recoge todas las reviews del producto
        $review_repo = $this->getDoctrine()->getRepository(ReviewTb::class);
        $reviews = $review_repo->findBy(['idProduct' => $product->getId()]);

        // y lo manda todo a la plantilla, renderizandola con el formulario
        return $this->render('product/index.html.twig', [
            'product' => $product,
            'features' => $features,
            'reviews' => $reviews,
            'form' => $form->createView(),
        ]);
    }

    //Renderiza los últimos productos que se han añadido en la web.
    public function latestProducts($nprods = NULL){

        //Si no se le indica el número,
        //se establece en 5 los productos a mostrar
        if (!$nprods) {
            $nprods = 5;
        }

        //recoge los 5 últimos añadidos
        $product_repo = $this->getDoctrine()->getRepository(ProductTb::class);
        $products = $product_repo->findBy([],['id' => 'DESC'],$nprods);

        //y les añade a cada uno sus características
        foreach ($products as $product) {
            $productFeature_repo = $this->getDoctrine()->getRepository(ProductFeatureTb::class);
            $features = $productFeature_repo->findBy(['idProduct' => $product->getId()]);
            foreach ($features as $feature) {
                $product->addFeature($feature);
            }

        }

        //para luego mandarlos a la plantilla
        //las características solo se usan en la tarjeta de producto,
        //por eso aparece anulado.
        return $this->render('_includes/blocks/products-latest.html.twig', [
            'products' => $products,
            'features' => ''

        ]);
    }

    //Esto renderiza una tarjeta de producto.
    //(podría pensar que no es necesario)
    public function productCard($product){

        return $this->render('_includes/blocks/product-minicard.html.twig', [
            'product' => $product
        ]);
    }

    //Busca un producto dependiendo de
    //lo que se introduzca en el campo de buscar
    public function productSearch(Request $request){

        //Primero obtiene el string de lo que se busca
        $search=$request->get('search');

        //Luego recoge la base de datos de los productos
        $product_repo = $this->getDoctrine()->getRepository(ProductTb::class);

        //con esto, hacemos una query con LIKE, para buscar productos
        $em=$this->getDoctrine()->getManager();
        $products = $result = $em->getRepository(ProductTb::class)->createQueryBuilder('o')
            ->where('o.name LIKE :search')
            ->setParameter('search', "%".$search."%")
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        //si no encuentra nada en los títulos, busca en las descripciones
        if (!$products){
            $products = $result = $em->getRepository(ProductTb::class)->createQueryBuilder('o')
                ->where('o.description LIKE :search')
                ->setParameter('search', "%".$search."%")
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);
        }

        //por último renderiza todos los productos encontrados
        return $this->render('product/product-search.html.twig', [
            'products' => $products,
            'search' => $search
        ]);
    }
}

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
use App\Entity\ProductsonorderTb;

// Importamos las clases relativas a respuestas y peticiones HTTP
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

//importamos los tipos de los formularios
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

// Importamos la clase Constraints para realizar validaciones sobre el formulario
use Symfony\Component\Validator\Constraints as Assert;

// Importamos la clase para codificar la contraseña
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/_index.html.twig');
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

    public function editUser(UserTb $user,UserPasswordEncoderInterface $encoder, Request $request){


        $form = $this->createFormBuilder()
			->add('nick', TextType::class,['required' => false])
			->add('email', TextType::class,['required' => false])
            ->add('name', TextType::class,['required' => false])
			->add('surname1', TextType::class,['required' => false])
            ->add('surname2', TextType::class,['required' => false])
            ->add('direction', TextType::class,['required' => false])
			->add('image', FileType::class,['required' => false])
			->add('phone', TextType::class,['required' => false])
			->add('password', PasswordType::class,['required' => false])
			->add('confirmPass', PasswordType::class,['required' => false])
            ->add('rol', ChoiceType::class,[
          'label' => 'Rol:',
          'required' => 'false',
          'empty_data' => NULL,
          'choices' => [
              'Usuario' => 'ROLE_USER',
              'Administrador' => 'ROLE_ADMIN']
          ])
        ->getForm();

		// Comprobamos la solicitud
		$form->handleRequest($request);

		// Comrpobamos si el formuario se ha registrado y es valido
		if ($form->isSubmitted() && $form->isValid()) {

			$pass = $form->get('password')->getData();
			$cPass = $form->get('confirmPass')->getData();

			if ($pass != $cPass){
                return $this->render('admin/user-edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
					'error' => 'Las contraseñas no son iguales.',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
			}

            $user_repo = $this->getDoctrine()->getRepository(UserTb::class);

            if ($user_repo->findBy(['email' => $form->get('email')->getData()])){
                return $this->render('admin/user-edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
					'error' => 'El email ya existe',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
			}

            if ($user_repo->findBy(['nick' => $form->get('nick')->getData()])){
                return $this->render('admin/user-edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
					'error' => 'El nombre de usuario ya existe',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
			}

            if ($form->get('name')->getData() != NULL) {
                $user->setName($form->get('name')->getData());
            }
            if ($form->get('email')->getData() != NULL) {
                $user->setEmail($form->get('email')->getData());
            }
            if ($form->get('nick')->getData() != NULL) {
                $user->setNick($form->get('nick')->getData());
                $slug = strtolower($user->getNick());
                $search = ['/', ',', '.','*',' ', 'á', 'é', 'í', 'ó', 'ú'];
                $replace = ['', '', '','','-', 'a', 'e', 'i', 'o', 'u'];
                $slug = str_replace($search, $replace, $slug);

                if ($user_repo->findBy(['slug' => $slug])){
                    $slug=$slug."x";
                }
                $user->setSlug($slug);
            }
            if ($form->get('surname1')->getData() != NULL) {
                $user->setSurname1($form->get('surname1')->getData());
            }
            if ($form->get('surname2')->getData() != NULL) {
                $user->setSurname2($form->get('surname2')->getData());
            }
            if ($form->get('direction')->getData() != NULL) {
                $user->setDirection($form->get('direction')->getData());
            }
            if ($form->get('phone')->getData() != NULL) {
                $user->setPhone($form->get('phone')->getData());
            }
            $user->setRol($form->get('rol')->getData());

            // Recuperamos el archivo
            $image = $form->get('image')->getData();

            if ($image){
                // Revisamos la extensión y creamos el nombre del archivo
                $image_name = $user->getSlug() . '.' . $image->guessExtension();

                // Movemos el archivo donde queremos que esté
                $image->move('assets/img/user', $image_name);
                $user->setImage($image_name);
            }

			if ($form->get('password')->getData()){
				$encodedPw = $encoder->encodePassword($user, $form->get('password')->getData());
				$user->setPassword($encodedPw);
			}

			$entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->render('admin/user-edit.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'error' => 'Éxito al editar el usuario',
                'icon' => '<i class="fas fa-check text-success"></i>'
            ]);
		} else {
                return $this->render('admin/user-edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView()
                ]);
		}
    }

    public function createUser(UserPasswordEncoderInterface $encoder, Request $request){


        $form = $this->createFormBuilder()
			->add('nick', TextType::class)
			->add('email', TextType::class)
            ->add('name', TextType::class,['required' => false])
			->add('surname1', TextType::class,['required' => false])
            ->add('surname2', TextType::class,['required' => false])
            ->add('direction', TextType::class,['required' => false])
			->add('image', FileType::class,['required' => false])
			->add('phone', TextType::class,['required' => false])
			->add('password', PasswordType::class)
			->add('confirmPass', PasswordType::class)
            ->add('rol', ChoiceType::class,[
                'label' => 'Rol:',
                'empty_data' => NULL,
                'choices' => [
                    'Usuario' => 'ROLE_USER',
                    'Administrador' => 'ROLE_ADMIN'
                ]
            ])
        ->getForm();

		// Comprobamos la solicitud
		$form->handleRequest($request);

		// Comrpobamos si el formuario se ha registrado y es valido
		if ($form->isSubmitted() && $form->isValid()) {

            $user = new UserTb();

			$pass = $form->get('password')->getData();
			$cPass = $form->get('confirmPass')->getData();

			if ($pass != $cPass){
				return $this->render('admin/user-create.html.twig', [
                    'form' => $form->createView(),
					'error' => 'Las contraseñas no son iguales.',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
			}

            if (!$pass){
                return $this->render('admin/user-create.html.twig', [
                    'form' => $form->createView(),
					'error' => 'El campo contraseña está vacio',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
            }

            $user_repo = $this->getDoctrine()->getRepository(UserTb::class);

            if ($user_repo->findBy(['email' => $form->get('email')->getData()])){
                return $this->render('admin/user-create.html.twig', [
                    'form' => $form->createView(),
					'error' => 'El email ya existe',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
			}

            if ($user_repo->findBy(['nick' => $form->get('nick')->getData()])){
                return $this->render('admin/user-create.html.twig', [
                    'form' => $form->createView(),
					'error' => 'El nombre de usuario ya existe',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
			}

            if ($form->get('name')->getData() != NULL) {
                $user->setName($form->get('name')->getData());
            }
            if ($form->get('email')->getData() != NULL) {
                $user->setEmail($form->get('email')->getData());
            }
            if ($form->get('nick')->getData() != NULL) {
                $user->setNick($form->get('nick')->getData());
                $slug = strtolower($user->getNick());
                $search = ['/', ',', '.','*',' ', 'á', 'é', 'í', 'ó', 'ú'];
                $replace = ['', '', '','','-', 'a', 'e', 'i', 'o', 'u'];
                $slug = str_replace($search, $replace, $slug);

                if ($user_repo->findBy(['slug' => $slug])){
                    $slug=$slug."x";
                }
                $user->setSlug($slug);
            }
            if ($form->get('surname1')->getData() != NULL) {
                $user->setSurname1($form->get('surname1')->getData());
            }
            if ($form->get('surname2')->getData() != NULL) {
                $user->setSurname2($form->get('surname2')->getData());
            }
            if ($form->get('direction')->getData() != NULL) {
                $user->setDirection($form->get('direction')->getData());
            }
            if ($form->get('phone')->getData() != NULL) {
                $user->setPhone($form->get('phone')->getData());
            }
            $user->setRol($form->get('rol')->getData());

            // Recuperamos el archivo
            $image = $form->get('image')->getData();

            if ($image){
                // Revisamos la extensión y creamos el nombre del archivo
                $image_name = $user->getSlug() . '.' . $image->guessExtension();

                // Movemos el archivo donde queremos que esté
                $image->move('assets/img/user', $image_name);
                $user->setImage($image_name);
            }

			if ($form->get('password')->getData()){
				$encodedPw = $encoder->encodePassword($user, $form->get('password')->getData());
				$user->setPassword($encodedPw);
			}

			$entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->render('admin/user-create.html.twig', [
                'form' => $form->createView(),
                'error' => 'Éxito al editar el usuario',
                'icon' => '<i class="fas fa-check text-success"></i>'
            ]);
		} else {
                return $this->render('admin/user-create.html.twig', [
                    'form' => $form->createView()
                ]);
		}
    }

    public function deleteUser(UserTb $user){

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
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

    public function deleteProduct(ProductTb $product){

        $items = $this  ->getDoctrine()
                        ->getRepository(ProductsonorderTb::class)
                        ->findBy(['idProduct' => $product->getId()]);

        $em = $this->getDoctrine()->getManager();
        foreach ($items as $item) {
            $em->remove($item);
        }
        $em->remove($product);

        $em->flush();

        $product_repo = $this->getDoctrine()->getRepository(ProductTb::class);
        $products = $product_repo->findAll();

        return $this->render('admin/products.html.twig', [
            'products' => $products
        ]);
    }

    public function editProduct(ProductTb $product,Request $request){
        $reviews= $this ->getDoctrine()
                        ->getRepository(ReviewTb::class)
                        ->findBy(['idProduct' => $product->getId()]);

        $form = $this->createFormBuilder()
            ->add('name', TextType::class,['required' => false])
            ->add('description', TextareaType::class,['data'=>$product->getDescription(),'required' => false])
            ->add('price', MoneyType::class,[
                'required' => false,
                'currency' => '€'
            ])
			->add('image', FileType::class,['required' => false])
            ->add('warehousequantity', IntegerType::class,['required' => false])
            ->add('category', EntityType::class,[
                    'class' => CategoryTb::class,
                    'choice_label' => 'name',
                    'data' => $product->getCategory()
                ])
        ->getForm();

		// Comprobamos la solicitud
		$form->handleRequest($request);

		// Comrpobamos si el formuario se ha registrado y es valido
		if ($form->isSubmitted() && $form->isValid()) {

            $product_repo = $this->getDoctrine()->getRepository(ProductTb::class);

            if ($product_repo->findBy(['name' => $form->get('name')->getData()])){
                return $this->render('admin/product-edit.html.twig', [
                    'product' => $product,
                    'reviews' => $reviews,
                    'form' => $form->createView(),
					'error' => 'Ese nombre de producto ya existe',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
			}

            if ($form->get('name')->getData() != NULL) {
                $product->setName($form->get('name')->getData());
                $slug = strtolower($product->getName());
                $search = ['/', ',', '.','*',' ', 'á', 'é', 'í', 'ó', 'ú'];
                $replace = ['', '', '','','-', 'a', 'e', 'i', 'o', 'u'];
                $slug = str_replace($search, $replace, $slug);

                if ($product_repo->findBy(['slug' => $slug])){
                    $slug=$slug."x";
                }
                $product->setSlug($slug);
            }
            if ($form->get('description')->getData() != NULL) {
                $product->setDescription($form->get('description')->getData());
            }
            if ($form->get('price')->getData() != NULL) {
                $price=$form->get('price')->getData();
                $price=str_replace(',', '.', $price);
                $product->setPrice($price);
            }
            if ($form->get('warehousequantity')->getData() != NULL) {
                $product->setWarehousequantity($form->get('warehousequantity')->getData());
            }
            if ($form->get('category')->getData() != NULL) {
                $product->setCategory($form->get('category')->getData());
            }

            // Recuperamos el archivo
            $image = $form->get('image')->getData();

            if ($image){
                // Revisamos la extensión y creamos el nombre del archivo
                $image_name = $product->getSlug() . '.' . $image->guessExtension();

                // Movemos el archivo donde queremos que esté
                $image->move('assets/img/user', $image_name);
                $product->setImage($image_name);
            }

			$entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();


            return $this->render('admin/product-edit.html.twig', [
                'product' => $product,
                'reviews' => $reviews,
                'form' => $form->createView(),
                'error' => 'Éxito al editar el producto',
                'icon' => '<i class="fas fa-check text-success"></i>'
            ]);
		} else {
                return $this->render('admin/product-edit.html.twig', [
                    'product' => $product,
                    'reviews' => $reviews,
                    'form' => $form->createView()
                ]);
		}
    }

    public function createProduct(CategoryTb $category = NULL,Request $request){

        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('description', TextareaType::class,['required' => false])
            ->add('price', MoneyType::class,[
                'currency' => '€'
            ])
            ->add('image', FileType::class)
            ->add('warehousequantity', IntegerType::class)
            ->add('category', EntityType::class,[
                    'class' => CategoryTb::class,
                    'choice_label' => 'name',
                    'data' => $category
                ])
        ->getForm();

        // Comprobamos la solicitud
        $form->handleRequest($request);

        // Comrpobamos si el formuario se ha registrado y es valido
        if ($form->isSubmitted()) {
        if ($form->isValid()) {

            $product = new ProductTb();

            $product_repo = $this->getDoctrine()->getRepository(ProductTb::class);

            if ($product_repo->findBy(['name' => $form->get('name')->getData()])){
                return $this->render('admin/product-create.html.twig', [
                    'form' => $form->createView(),
                    'error' => 'Ese nombre de producto ya existe',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
            }

            if ($form->get('name')->getData() != NULL) {
                $product->setName($form->get('name')->getData());
                $slug = strtolower($product->getName());
                $search = ['/', ',', '.','*',' ', 'á', 'é', 'í', 'ó', 'ú'];
                $replace = ['', '', '','','-', 'a', 'e', 'i', 'o', 'u'];
                $slug = str_replace($search, $replace, $slug);

                if ($product_repo->findBy(['slug' => $slug])){
                    $slug=$slug."x";
                }
                $product->setSlug($slug);
            }
            if ($form->get('description')->getData() != NULL) {
                $product->setDescription($form->get('description')->getData());
            }
            if ($form->get('price')->getData() != NULL) {
                $price=$form->get('price')->getData();
                $price=str_replace(',', '.', $price);
                $product->setPrice($price);
            }
            if ($form->get('warehousequantity')->getData() != NULL) {
                $product->setWarehousequantity($form->get('warehousequantity')->getData());
            }
            if ($form->get('category')->getData() != NULL) {
                $product->setCategory($form->get('category')->getData());
            }

            // Recuperamos el archivo
            $image = $form->get('image')->getData();

            if ($image){
                // Revisamos la extensión y creamos el nombre del archivo
                $image_name = $product->getSlug() . '.' . $image->guessExtension();

                // Movemos el archivo donde queremos que esté
                $image->move('assets/img/user', $image_name);
                $product->setImage($image_name);
            }

            $entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();


            return $this->render('admin/product-create.html.twig', [
                'form' => $form->createView(),
                'error' => 'Éxito al editar el producto',
                'icon' => '<i class="fas fa-check text-success"></i>'
            ]);
        } else {
            return $this->render('admin/product-create.html.twig', [
                'form' => $form->createView(),
                'error' => 'Ha habido un error al enviar el formulario',
                'icon' => '<i class="fas fa-times-circle text-danger"></i>'
            ]);
        }
        } else {
                return $this->render('admin/product-create.html.twig', [
                    'form' => $form->createView()
                ]);
        }
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

    public function showReview(ReviewTb $review){
                return $this->render('admin/review-show.html.twig', [
                    'review' => $review
                ]);
    }

    #######################################################
    ####################### ORDERS ########################
    #######################################################
    public function varTest($var){
        die($this->render('test.html.twig',[
            'var' => $var
        ]));
    }

    public function updateOrder(OrderTb $order){

        $items = $this  ->getDoctrine()
                            ->getRepository(ProductsonorderTb::class)
                            ->findBy(['idOrder' => $order->getId()]);

        $totalPrice = 0;
        foreach ($items as $item) {
            $price = $this  ->getDoctrine()
                            ->getRepository(ProductTb::class)
                            ->findOneBy(['id' => $item->getIdProduct()])
                            ->getPrice();

            $unitPrice = (Float)$item->getIdProduct()->getPrice();
            $quantity = (Int)$item->getQuantity();

            $totalPrice = floatval($totalPrice) + number_format(floatval($unitPrice)*floatval($quantity), 2,".","");
        }
        $order->setTotalPrice($totalPrice);

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        $empties = $items->findBy(['idProduct' =>'']);

        foreach ($empties as $item) {
            $em->remove($item);
        }

        $empties = $items->findBy(['idProduct' =>NULL]);

        foreach ($empties as $item) {
            $em->remove($item);
        }

        $empties = $items->findBy(['idOrder' =>'']);

        foreach ($empties as $item) {
            $em->remove($item);
        }

        $empties = $items->findBy(['idOrder' =>NULL]);

        foreach ($empties as $item) {
            $em->remove($item);
        }

        $em->flush();
    }

    public function updateOrders($orders){
        $orders = $this ->getDoctrine()
                        ->getRepository(OrderTb::class)
                        ->findAll();

        foreach ($orders as $order) {

            $items = $this  ->getDoctrine()
                            ->getRepository(ProductsonorderTb::class)
                            ->findBy(['idOrder' => $order->getId()]);

            $totalPrice = 0;
            foreach ($items as $item) {
                $price = $this  ->getDoctrine()
                                ->getRepository(ProductTb::class)
                                ->findOneBy(['id' => $item->getIdProduct()])
                                ->getPrice();

                $unitPrice = (Float)$item->getIdProduct()->getPrice();
                $quantity = (Int)$item->getQuantity();

                $totalPrice = floatval($totalPrice) + number_format(floatval($unitPrice)*floatval($quantity), 2,".","");
            }
            $order->setTotalPrice($totalPrice);
        }

        $em = $this->getDoctrine()->getManager();
        foreach ($orders as $order) {
            $em->persist($order);
        }
        $em->flush();

        //limpiamos las id vacias
        $empties = $items->findBy(['idProduct' =>'']);

        foreach ($empties as $item) {
            $em->remove($item);
        }

        $empties = $items->findBy(['idProduct' =>NULL]);

        foreach ($empties as $item) {
            $em->remove($item);
        }

        $empties = $items->findBy(['idOrder' =>'']);

        foreach ($empties as $item) {
            $em->remove($item);
        }

        $empties = $items->findBy(['idOrder' =>NULL]);

        foreach ($empties as $item) {
            $em->remove($item);
        }

        $em->flush();
    }

    public function updateAllOrders(){
        $orders = $this ->getDoctrine()
                        ->getRepository(OrderTb::class)
                        ->findAll();

        $this->updateOrders($orders);
    }

    public function orders(){
        $orders = $this ->getDoctrine()
                        ->getRepository(OrderTb::class)
                        ->findAll();

        $this->updateOrders($orders);

        return $this->render('admin/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    public function activeOrder(OrderTb $order){

        $this->updateOrder($order);

        if ($order->getActive())
            $order->setActive(0);
        else
            $order->setActive(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        return $this->RedirectToRoute('admin-orders');
    }

    public function deleteOrder(OrderTb $order){

        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();

        updateOrder($order);

        return $this->RedirectToRoute('admin-orders');
    }

    public function deleteOrderItem( $product, $order){

        $list_repo = $this->getDoctrine()->getRepository(ProductsonorderTb::class);
        $targetItem = $list_repo->findOneBy(['idProduct' => $product,'idOrder' => $order]);

        $em = $this->getDoctrine()->getManager();
        $em->remove($targetItem);
        $em->flush();

        $order=$this->getDoctrine()
                    ->getRepository(OrderTb::class)
                    ->findOneBy(['id' => $order]);

        $this->updateOrder($order);

        return $this->RedirectToRoute('admin-orders');
    }

    public function editOrder(OrderTb $order,Request $request, UserPasswordEncoderInterface $encoder){

        $products=[];
        $this->updateOrder($order);

        $relations = $this  ->getDoctrine()
                            ->getRepository(ProductsonorderTb::class)
                            ->findBy(['idOrder' => $order->getId()]);

        foreach ($relations as $relation) {
            $relationProducts = $this    ->getDoctrine()
                                ->getRepository(ProductTb::class)
                                ->findBy(['id' => $relation->getIdProduct()]);

            //es un array de 1 elemento.
            $product=$relationProducts[0];

            $quantity = $relation->getQuantity();
            $product->setQuantity($quantity);
            $products[]=$product;
        }


        $form = $this->createFormBuilder()
            ->add('name', TextType::class,['required' => false])
            ->add('totalPrice', MoneyType::class,[
                'required' => false,
                'currency' => '€'
            ])
            ->add('direction', TextType::class,['required' => false])
            ->add('deliveryStatus', TextType::class,['required' => false,'disabled'=>true,'data'=>$order->getDeliveryStatus()])
        ->getForm();

		// Comprobamos la solicitud
		$form->handleRequest($request);

		// Comrpobamos si el formuario se ha registrado y es valido
		if ($form->isSubmitted() && $form->isValid()) {

            $order_repo = $this->getDoctrine()->getRepository(OrderTb::class);

            if ($order_repo->findBy(['name' => $form->get('name')->getData()])){
                return $this->render('admin/order-edit.html.twig', [
                    'order' => $order,
                    'products' => $products,
                    'form' => $form->createView(),
					'error' => 'Este nombre de cesta ya existe',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
			}

            if ($form->get('name')->getData() != NULL) {
                $order->setName($form->get('name')->getData());
            }
            if ($form->get('direction')->getData() != NULL) {
                $order->setDirection($form->get('direction')->getData());
            }
            if ($form->get('deliveryStatus')->getData() != NULL) {
                $order->setDeliveryStatus($form->get('deliveryStatus')->getData());
            }

			$entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();


            return $this->render('admin/order-edit.html.twig', [
                'order' => $order,
                'products' => $products,
                'form' => $form->createView(),
                'error' => 'Éxito al editar el producto',
                'icon' => '<i class="fas fa-check text-success"></i>'
            ]);
		} else {
                return $this->render('admin/order-edit.html.twig', [
                    'order' => $order,
                    'products' => $products,
                    'form' => $form->createView()
                ]);
		}
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

    public function deleteMessage(ContactformTb $message){

        $em = $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();

        return $this->RedirectToRoute('admin-messages');
    }

    public function showMessage(ContactformTb $message){

        return $this->render('admin/message-show.html.twig', [
            'message' => $message
        ]);
    }

    #######################################################
    ####################### CATEGORIES ####################
    #######################################################

    public function categories(){
        $category_repo = $this->getDoctrine()->getRepository(CategoryTb::class);
        $categories = $category_repo->findAll();

        return $this->render('admin/categories.html.twig', [
            'categories' => $categories
        ]);
    }

    public function deleteCategory(CategoryTb $category){

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $category_repo = $this->getDoctrine()->getRepository(CategoryTb::class);
        $categories = $category_repo->findAll();

        return $this->render('admin/categories.html.twig', [
            'categories' => $categories
        ]);
    }

    public function editCategory(CategoryTb $category,Request $request){

        $category_repo = $this->getDoctrine()->getRepository(CategoryTb::class);

        $products= $this->getDoctrine()
                        ->getRepository(ProductTb::class)
                        ->findBy(['category' => $category->getId()]);

        $form = $this->createFormBuilder()
            ->add('name', TextType::class,['required' => false])
            ->add('description', TextareaType::class,['data'=>$category->getDescription(),'required' => false])
            ->add('image', FileType::class,['required' => false])
            ->add('parentSlg', EntityType::class,[
                    'class' => CategoryTb::class,
                    'choice_label' => 'name',
                    'data' => $category_repo->findOneBy(['slug' => $category->getParentSlg()]),
                    'required' => false
                ])
        ->getForm();

        // Comprobamos la solicitud
        $form->handleRequest($request);

        // Comrpobamos si el formuario se ha registrado y es valido
        if ($form->isSubmitted() && $form->isValid()) {

            if ($category_repo->findBy(['name' => $form->get('name')->getData()])){
                return $this->render('admin/category-edit.html.twig', [
                    'category' => $category,
                    'products' => $products,
                    'form' => $form->createView(),
                    'error' => 'Ese nombre de categoria ya existe',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
            }

            if ($form->get('name')->getData() != NULL) {

                $category->setName($form->get('name')->getData());

                $slug = strtolower($category->getName());
                $search = ['/', ',', '.','*',' ', 'á', 'é', 'í', 'ó', 'ú'];
                $replace = ['', '', '','','-', 'a', 'e', 'i', 'o', 'u'];
                $slug = str_replace($search, $replace, $slug);

                if ($category_repo->findBy(['slug' => $slug])){
                    $slug=$slug."x";
                }
                $category->setSlug($slug);
            }
            if ($form->get('description')->getData() != NULL) {
                $category->setDescription($form->get('description')->getData());
            }
            $parent=$form->get('parentSlg')->getData();
            if ($parent != NULL) {
                $category->setParentSlg($parent->getSlug());
            } else {
                $category->setParentSlg(NULL);
            }


            // Recuperamos el archivo
            $image = $form->get('image')->getData();

            if ($image){
                // Revisamos la extensión y creamos el nombre del archivo
                $image_name = $category->getSlug() . '.' . $image->guessExtension();

                // Movemos el archivo donde queremos que esté
                $image->move('assets/img/category', $image_name);
                $category->setImage($image_name);
            }

                $entityManager= $this->getDoctrine()->getManager();
                $entityManager->persist($category);
                $entityManager->flush();



            return $this->render('admin/category-edit.html.twig', [
                'products' => $products,
                'category' => $category,
                'form' => $form->createView(),
                'error' => 'Éxito al editar la categoria',
                'icon' => '<i class="fas fa-check text-success"></i>'
            ]);
        } else {
                return $this->render('admin/category-edit.html.twig', [
                    'products' => $products,
                    'category' => $category,
                    'form' => $form->createView()
                ]);
        }
    }

    public function createCategory(Request $request){

        $category_repo = $this->getDoctrine()->getRepository(CategoryTb::class);

        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('description', TextareaType::class,['required' => false])
            ->add('image', FileType::class)
            ->add('parentSlg', EntityType::class,[
                    'class' => CategoryTb::class,
                    'choice_label' => 'name',
                    'required' => false
                ])
        ->getForm();

        // Comprobamos la solicitud
        $form->handleRequest($request);

        // Comrpobamos si el formuario se ha registrado y es valido
        if ($form->isSubmitted() && $form->isValid()) {

            $category = new CategoryTb;

            if ($category_repo->findBy(['name' => $form->get('name')->getData()])){
                return $this->render('admin/category-create.html.twig', [
                    'form' => $form->createView(),
                    'error' => 'Ese nombre de categoria ya existe',
                    'icon' => '<i class="fas fa-times-circle text-danger"></i>'
                ]);
            }

            if ($form->get('name')->getData() != NULL) {

                $category->setName($form->get('name')->getData());

                $slug = strtolower($category->getName());
                $search = ['/', ',', '.','*',' ', 'á', 'é', 'í', 'ó', 'ú'];
                $replace = ['', '', '','','-', 'a', 'e', 'i', 'o', 'u'];
                $slug = str_replace($search, $replace, $slug);

                if ($category_repo->findBy(['slug' => $slug])){
                    $slug=$slug."x";
                }
                $category->setSlug($slug);
            }
            if ($form->get('description')->getData() != NULL) {
                $category->setDescription($form->get('description')->getData());
            }
            $parent=$form->get('parentSlg')->getData();
            if ($parent != NULL) {
                $category->setParentSlg($parent->getSlug());
            } else {
                $category->setParentSlg(NULL);
            }


            // Recuperamos el archivo
            $image = $form->get('image')->getData();

            if ($image){
                // Revisamos la extensión y creamos el nombre del archivo
                $image_name = $category->getSlug() . '.' . $image->guessExtension();

                // Movemos el archivo donde queremos que esté
                $image->move('assets/img/category', $image_name);
                $category->setImage($image_name);
            }

                $entityManager= $this->getDoctrine()->getManager();
                $entityManager->persist($category);
                $entityManager->flush();



            return $this->render('admin/category-create.html.twig', [
                'form' => $form->createView(),
                'error' => 'Éxito al crear la categoria',
                'icon' => '<i class="fas fa-check text-success"></i>'
            ]);
        } else {
                return $this->render('admin/category-create.html.twig', [
                    'form' => $form->createView()
                ]);
        }
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// Importamos las clases relativas a respuestas y peticiones HTTP
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

//importamos las entidades usadas
use App\Entity\ProductTb;
use App\Entity\OrderTb;
use App\Entity\ReviewTb;
use App\Entity\ProductsonorderTb;

//cargamos los tipos de formularios necesarios
use Symfony\Component\Form\Extension\Core\Type\TextType;

//Importamos el componente que tiene el usuario actual
use Symfony\Component\Security\Core\Security;

class OrderController extends AbstractController
{
    //esto se usa para volver a la url anterior

    ///////////////////////////////////////////////////////////
    //estas son funciones para ayudarme a organizar el código//
    ///////////////////////////////////////////////////////////

    //para testear variables. Muy útil
    public function varTest($var){
        die($this->render('test.html.twig',[
            'var' => $var
     ]));

    }//$this->varTest($orders);

    //Devuelve las ordenes del usuario logueado
    public function myOrders(Security $security){
        $orders = $this ->getDoctrine()
                        ->getRepository(OrderTb::class)
                        ->findBy(['idUser' => $security->getUser()->getId()]);
        return $orders;
    }

    //Devuelve una orden cualquiera del usuario actual
    public function myOrder(Security $security){
        $order = $this ->getDoctrine()
                        ->getRepository(OrderTb::class)
                        ->findOneBy(['idUser' => $security->getUser()->getId()]);
        return $order;
    }

    //Devuelve la orden seleccionada del usuario actual
    public function currentOrder(Security $security){
        $order = $this ->getDoctrine()
                        ->getRepository(OrderTb::class)
                        ->findOneBy(['idUser' => $security->getUser()->getId(),'selected'=> true]);
        return $order;
    }

    //devuelve los items de una cesta
    public function itemsOnOrder(OrderTb $order){

        //Primero busca los productos de una cesta
        $search = $this  ->getDoctrine()
                        ->getRepository(ProductsonorderTb::class)
                        ->findBy(['idOrder' => $order->getId()]);
        $items=[];

        //y en cada item,
        foreach ($search as $item) {
            $quantity = $item->getQuantity();
            $item = $this   ->getDoctrine()
                            ->getRepository(ProductTb::class)
                            ->findOneBy(['id' => $item->getIdProduct()]);
            //agrega la cantidad al mismo producto,
            //para pasarlo a donde sea necesario
            $item->setQuantity($quantity);
            $item->setTotal($quantity*$item->getPrice());
            $items[] = $item;
        }

        //y devuelve el array de items
        return $items;
    }

    //con esta funcion borramos un item de la cesta
    public function deleteOrderItem( $product, $order){

        if ($product && $order) {
            $list_repo = $this->getDoctrine()->getRepository(ProductsonorderTb::class);
            $targetItem = $list_repo->findOneBy(['idProduct' => $product,'idOrder' => $order]);

            $em = $this->getDoctrine()->getManager();
            if ($targetItem) {
                $em->remove($targetItem);
                $em->flush();

                $order=$this->getDoctrine()
                            ->getRepository(OrderTb::class)
                            ->findOneBy(['id' => $order]);

                $this->updateOrder($order);
            }
        }

        return $this->RedirectToRoute('cesta');
    }

    //con esta función, actualizamos la base de datos de una orden
    //actualizamos el precio y borramos registros de la relacion many<->many
    public function updateOrder(OrderTb $order){

        //recoge los items de la orden
        $items = $this  ->getDoctrine()
                            ->getRepository(ProductsonorderTb::class)
                            ->findBy(['idOrder' => $order->getId()]);

        $totalPrice = 0;//inicia el precio en 0
        foreach ($items as $item) {

            //recogemos las unidades y el precio de 1 item.
            $unitPrice = (Float)$item->getIdProduct()->getPrice();
            $quantity = (Int)$item->getQuantity();

            //multiplicando el precio del producto por su unidad
            //obtenemos el precio total de ese producto
            //vamos sumando todos los items.
            $totalPrice = floatval($totalPrice) + number_format(floatval($unitPrice)*floatval($quantity), 2,".","");
        }
        //el precio total será la suma de todos los productos
        $order->setTotalPrice($totalPrice);

        //y se actualiza la base de datos con el precio nuevo
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        //también se actualizan los items
        //si se ve que alguno de ellos
        //no tiene producto o cesta asociados se borra
        foreach ($items as $item) {
            if ($item->getIdProduct() == NULL || $item->getIdProduct() == "") {
                $em->remove($item);
            }
            if ($item->getIdOrder() == NULL || $item->getIdOrder() == "") {
                $em->remove($item);
            }
        }

        $em->flush();
    }

    //con esta funcion seleccionamos la cesta actual
    //como la que estamos usando actualmente
    public function selectOrder(OrderTb $order,Security $security){

        //primero recogemos las cesta del usuario activo
        $orders = $this->myOrders($security);
        $em = $this->getDoctrine()->getManager();

        //hacemos que todas las cestas se deseleccionen
        foreach ($orders as $ord) {
            $ord->setSelected(false);
            $em->persist($ord);
        }

        //y seleccionamos solo la que queremos
        $order->setSelected(true);
        $em->persist($order);
        $em->flush();

        //tras esto nos redireccionamos a la cesta seleccionada
        return $this->RedirectToRoute('cesta');
    }

    //devuelve el item con la cantidad si existe, o falso si no.
    public function productIteminOrder(ProductTb $product, Security $security){

        //recoge los items de la orden seleccionada
        $itemsInCurrentOrder = $this->itemsOnOrder($this->currentOrder($security));

        $found = false; //inicia la variable found
        //busca el item
        foreach ($itemsInCurrentOrder as $item) {
            if ($item->getId() == $product->getId()) {
                $found = true;
                $targetItem = $item;
            }
        }


        if ($found) { //si lo ha encontrado
            return $targetItem; //lo retorna
        } else {
            return false; //sino, devuelve falso
        }
    }

    //devuelve el recibo, donde aparecen los items con sus precios finales
    public function payOrder(OrderTb $order,Security $security){

        //recoge todos los items
        $item_repo = $this->getDoctrine()->getRepository(ProductsonorderTb::class);
        $items = $item_repo->findBy([ 'idOrder' => $order->getId() ]);

        //y renderiza la plantilla con el recibo
        return $this->render('order/order-pay.html.twig',[
            "order" => $order,
            'items' => $items
        ]);
    }

    //Si se realiza el pago, cambia el estado de la cesta
    // y muestra un mensaje en una nueva plantilla
    public function payedOrder(OrderTb $order,Security $security){

        //si hay usuario activo
        if ($security->getUser()) {
            $status = $order->getDeliveryStatus();
            if ($status == "OPEN") { // y la orden está abierta
                $status = "PAID"; //pasa a estar pagada
                $order->SetDeliveryStatus($status);
                $order->SetDate(new \DateTime()); //a fecha de hoy
            }

            //introducimos los cambios en la BD
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            //y devolvemos el render de la plantilla
            return $this->render('order/order-payed.html.twig',[
                "order" => $order
            ]);
        } else {
            //si no hay usuario volvemos a la página de inicio
            return $this->RedirectToRoute('index');
        }
    }

    ////////////////////////////////////
    // estas ya son funciones de ruta //
    ////////////////////////////////////

    //cuando estamos logueados, al pulsar en menú de carritos nos muestra
    //los carritos que tenemos
    public function orderMenu(Security $security){

        if ($security->getUser()) { //si existe usuario activo
            $orders = $this->myOrders($security); //devuelve sus cestas

            // y las muestra en el menú de órdenes
            return $this->render('order/order-menu.html.twig', [
                'orders' => $orders
            ]);
        } else {
            return $this->RedirectToRoute('index');
        }
    }

    //Cuando hacemos click en algún boton que nos lleve al carrito,
    //se activa esta funcion y nos devuelve la página del carrito
    public function orderShow(OrderTb $order = NULL,Security $security)
    {

        if ($security->getUser()) {
            if ($order == NULL || !$order) {
                //Si no le pasamos orden, devuelve la que estamos usando
                $order = $this->currentOrder($security);
            }

            if ($order == NULL || !$order) {
                //esto es para pruebas. Si sigue sin haber ordenes,
                //le pasamos una cualquiera
                $order = $this->myOrder($security);
            }

            if (!$order || $order == NULL) {
                return $this->RedirectToRoute('cestas');
            }

            //primero actualiza la orden antes de pasarla
            $this->updateOrder($order);

            //y pasa los items a la plantilla
            $items = $this->itemsOnOrder($order);

            //la plantilla recibe la orden y los items
            return $this->render('order/order-show.html.twig', [
                'order' => $order,
                'items' => $items
            ]);
        } else {
            return $this->RedirectToRoute('index');
        }
    }

    //esta funcion añade un item al carrito
    public function orderAddItem(Request $request,Security $security)
    {
        //primero recoge el producto de la BD
        //a través del request GET
        $em = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()
                        ->getRepository(ProductTb::class)
                        ->findOneBy(['id'=> $request->query->get('product-id')]);

        //despues comprueba si hay usuario activo
        if ($security->getUser()) {

            //recoge la cantidad
            $quantity = $request->query->get('quantity');

            //busca el item en la cesta
            $found = $this->productIteminOrder($product,$security);

            if ($found) { //si existe
                $item = $this   ->getDoctrine()
                                ->getRepository(ProductsonorderTb::class)
                                ->findOneBy(['idProduct'=> $product->getId()]);
                $quantity = $product->getQuantity()+$quantity;
                //le añade la cantidad

                //y lo agrega a la query
                $item->setQuantity($quantity);
                $em->persist($item);

            } else {
                //si no existe crea un nuevo item
                $newItem = new ProductsonorderTb();

                $newItem->setQuantity($quantity);
                $newItem->setIdProduct($product);
                $newItem->setIdOrder($this->currentOrder($security));
                $em->persist($newItem);
                //le agrega todos los datos a la query
            }

            //y lo carga en la BD
            $em->flush();

            //después redirige de nuevo al producto donde estabamos
            return $this->redirect("/producto/".$product->getSlug(), 308);
        } else {
            return $this->RedirectToRoute('index');
        }
    }

    //borra una orden de la BD
    public function orderDelete(OrderTb $order, Security $security){

        //si es un usuario logueado
        if ($security->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($order);
            $em->flush();
            //puede borrar su orden
        }
        //y volver al menú de cestas
        return $this->RedirectToRoute('cestas');
    }

    //Borra un comentario de un producto
    public function reviewDelete(Request $request, Security $security){

        //recoge el id del comentario en el request
        $request->query->get('idreview');
        $review = $this ->getDoctrine()
                        ->getRepository(ReviewTb::class)
                        ->findOneBy([
                            'id' => $request->query->get('idreview')]);
        //para después encontrarlo en la base de datos

        //si existe un usuario activo puede borrar el comentario
        if ($security->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($review);
            $em->flush();
        }

        //y redirige de vuelta a la url donde estabamos
        $url="/producto/".$request->query->get('slug');
        return $this->redirect($url, 308);
    }

    //Este es el formulario para crear cestas
    public function orderCreate(Request $request, Security $security){

        //creamos el formulario
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('direction', TextType::class,['required' => false])
        ->getForm();

        // Comprobamos la solicitud
        $form->handleRequest($request);

        // Comrpobamos si el formuario se ha registrado y es valido
        if ($form->isSubmitted() && $form->isValid()) {
            $user=$security->getUser();

            $order = new OrderTb();

            //se recoge el repositorio de las cestas
            $order_repo = $this->getDoctrine()->getRepository(OrderTb::class);
            //se comprueba si el nombre es nulo, si no es así se procede
            if ($form->get('name')->getData() != NULL) {

                $order->setName($form->get('name')->getData());
                //se forma un slug a partir del nick
                // de usuario y nombre de orden
                $slug = strtolower($user->getNick().'_'.$order->getName());
                $search = ['/', ',', '.','*',' ', 'á', 'é', 'í', 'ó', 'ú'];
                $replace = ['', '', '','','-', 'a', 'e', 'i', 'o', 'u'];
                $slug = str_replace($search, $replace, $slug);

                if ($order_repo->findBy(['slug' => $slug])){
                    $slug=$slug."x";
                }
                $order->setSlug($slug);
                $order->setSelected(false);
                $order->setTotalPrice(0);
                $order->setDeliveryStatus('OPEN');
                $order->setIdUser($user);
            }

            $select = $order;   // prevención por si
                                // entityManager modifica la entidad

            $entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush(); // se carga todo en la base de datos

            $this->selectOrder($select,$security);
            //seleccionamos la orden actual
            //esta funcion ya existia más arriba

            //y volvemos a las cestas tras crearla
            return $this->redirectToRoute('cestas');
        } else {
            //si no se envió el formulario, se crea el mismo
            return $this->render('order/order-create.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }
}

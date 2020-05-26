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

    public function myOrder(Security $security){
        $order = $this ->getDoctrine()
                        ->getRepository(OrderTb::class)
                        ->findOneBy(['idUser' => $security->getUser()->getId()]);
        return $order;
    }

    public function currentOrder(Security $security){
        $order = $this ->getDoctrine()
                        ->getRepository(OrderTb::class)
                        ->findOneBy(['idUser' => $security->getUser()->getId(),'selected'=> true]);
        return $order;
    }

    //devuelve los items de una cesta
    public function itemsOnOrder(OrderTb $order){

        $search = $this  ->getDoctrine()
                        ->getRepository(ProductsonorderTb::class)
                        ->findBy(['idOrder' => $order->getId()]);
        $items=[];
        foreach ($search as $item) {
            $quantity = $item->getQuantity();
            $item = $this   ->getDoctrine()
                            ->getRepository(ProductTb::class)
                            ->findOneBy(['id' => $item->getIdProduct()]);
            $item->setQuantity($quantity);
            $item->setTotal($quantity*$item->getPrice());
            $items[] = $item;


        }
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

        $orders = $this->myOrders($security);
        $em = $this->getDoctrine()->getManager();

        foreach ($orders as $ord) {
            $ord->setSelected(false);
            $em->persist($ord);
        }

        $order->setSelected(true);
        $em->persist($order);
        $em->flush();

        return $this->RedirectToRoute('cesta');
    }

    public function productIteminOrder(ProductTb $product, Security $security){

        $itemsInCurrentOrder = $this->itemsOnOrder($this->currentOrder($security));

        $found = false;
        foreach ($itemsInCurrentOrder as $item) {
            if ($item->getId() == $product->getId()) {
                $found = true;
                $targetItem = $item;
            }
        }

        if ($found) {
            return $targetItem;
        } else {
            return false;
        }
    }

    public function payOrder(OrderTb $order,Security $security){

        $item_repo = $this->getDoctrine()->getRepository(ProductsonorderTb::class);
        $items = $item_repo->findBy([ 'idOrder' => $order->getId() ]);

        return $this->render('order/order-pay.html.twig',[
            "order" => $order,
            'items' => $items
        ]);
        //return $this->RedirectToRoute('cesta-pagada');
    }

    public function payedOrder(OrderTb $order,Security $security){

        if ($security->getUser()) {
            $status = $order->getDeliveryStatus();
            if ($status == "OPEN") {
                $status = "PAID";
                $order->SetDeliveryStatus($status);
                $order->SetDate(new \DateTime());
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            return $this->render('order/order-payed.html.twig',[
                "order" => $order
            ]);
        } else {
            return $this->RedirectToRoute('index');
        }
    }

    ////////////////////////////////////
    // estas ya son funciones de ruta //
    ////////////////////////////////////

    //cuando estamos logueados, al pulsar en menú de carritos nos muestra
    //los carritos que tenemos
    public function orderMenu(Security $security){

        if ($security->getUser()) {
            $orders = $this->myOrders($security);

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
        $em = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()
                        ->getRepository(ProductTb::class)
                        ->findOneBy(['id'=> $request->query->get('product-id')]);
        if ($security->getUser() || !$product || $product == NULL) {
            $quantity = $request->query->get('quantity');

            $found = $this->productIteminOrder($product,$security);





            if ($found) {
                $item = $this   ->getDoctrine()
                                ->getRepository(ProductsonorderTb::class)
                                ->findOneBy(['idProduct'=> $product->getId()]);
                $quantity = $product->getQuantity()+$quantity;

                $item->setQuantity($quantity);
                $em->persist($item);

            } else {
                $newItem = new ProductsonorderTb();

                $newItem->setQuantity($quantity);
                $newItem->setIdProduct($product);
                $newItem->setIdOrder($this->currentOrder($security));
                $em->persist($newItem);
            }






            $em->flush();


            return $this->redirect("/producto/".$product->getSlug(), 308);
        } else {
            return $this->RedirectToRoute('index');
        }
    }

    public function orderDelete(OrderTb $order, Security $security){

        if ($security->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($order);
            $em->flush();
        }

        return $this->RedirectToRoute('cestas');
    }

    public function reviewDelete(Request $request, Security $security){

        $request->query->get('idreview');
        $review = $this ->getDoctrine()
                        ->getRepository(ReviewTb::class)
                        ->findOneBy([
                            'id' => $request->query->get('idreview')]);

        if ($security->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($review);
            $em->flush();
        }

        $url="/producto/".$request->query->get('slug');
        return $this->redirect($url, 308);
    }

    public function orderCreate(Request $request, Security $security){

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

            $order_repo = $this->getDoctrine()->getRepository(OrderTb::class);

            if ($form->get('name')->getData() != NULL) {
                $order->setName($form->get('name')->getData());
                $slug = strtolower($user->getNick().'_'.$order->getName());
                $search = ['/', ',', '.','*',' ', 'á', 'é', 'í', 'ó', 'ú'];
                $replace = ['', '', '','','-', 'a', 'e', 'i', 'o', 'u'];
                $slug = str_replace($search, $replace, $slug);

                if ($order_repo->findBy(['slug' => $slug])){
                    $slug=$slug."x";
                }
                $order->setSlug($slug);
                $order->setSelected(true);
                $order->setTotalPrice(0);
                $order->setDeliveryStatus('OPEN');
                $order->setIdUser($user);
            }

            $entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('cestas');
        } else {
            return $this->render('order/order-create.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }


}

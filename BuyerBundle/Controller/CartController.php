<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\BuyerBundle\Controller;

use HarvestCloud\MarketPlace\BuyerBundle\Controller\BuyerController as Controller;
use Symfony\Component\HttpFoundation\Response;
use HarvestCloud\CoreBundle\Entity\OrderCollection;
use HarvestCloud\CoreBundle\Entity\Order;
use HarvestCloud\CoreBundle\Entity\OrderLineItem;

/**
 * CartController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2012-04-10
 */
class CartController extends Controller
{
    /**
     * show
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-20
     */
    public function showAction()
    {
        $orderCollection = $this->getCurrentCart();

        if ($orderCollection)
        {
            return $this->render('HarvestCloudMarketPlaceBuyerBundle:Cart:show.html.twig', array(
                'orderCollection'   => $orderCollection,
            ));
        }

        return new Response('');
    }

    /**
     * Mini cart
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-04-10
     */
    public function miniAction()
    {
        $session = $this->getRequest()->getSession();

        $orderCollection = $this->getRepo('OrderCollection')
            ->find($session->get('cart_id'));

        if ($orderCollection)
        {
            return $this->render('HarvestCloudMarketPlaceBuyerBundle:Cart:mini.html.twig', array(
                'orderCollection'   => $orderCollection,
            ));
        }

        return new Response('');
    }

    /**
     * sub_total
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-23
     */
    public function sub_totalAction()
    {
        $session = $this->getRequest()->getSession();

        $orderCollection = $this->getCurrentCart();

        if ($orderCollection)
        {
            $sub_total = $orderCollection->getSubTotal();
        }
        else
        {
            $sub_total = 0;
        }

        return new Response('$'.number_format($sub_total, 2));
    }

    /**
     * add Product to Cart
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-04-09
     * @todo   refactor as much as possible to model
     * @todo   refactor finding order for Product
     *
     * @param  int  $product_id
     */
    public function addProductAction($product_id)
    {
        $product = $this->getDoctrine()
            ->getRepository('HarvestCloudCoreBundle:Product')
            ->find($product_id);

        if (!$product)
        {
            throw $this->createNotFoundException('No product found for id '.$product_id);
        }

        // Find OrderCollection
        $session = $this->getRequest()->getSession();

        $buyer = $this->getCurrentProfile();

        $orderCollection = $this->getRepo('OrderCollection')
            ->find($session->get('cart_id'));

        // Create an OrderCollection if we don't have one
        if (!$orderCollection)
        {
            $orderCollection = new OrderCollection();
            $orderCollection->setBuyer($buyer);
        }

        // Create an Order if we don't have one
        if (!$order = $orderCollection->getOrderForSeller($product->getSeller()))
        {
            $order = $orderCollection->createOrderForSeller($product->getSeller());
        }

        if ($lineItem = $order->getLineItemForProduct($product))
        {
            $lineItem->setQuantity($lineItem->getQuantity()+1);
        }
        else
        {
            $lineItem = $order->createLineItemForProduct($product);
        }

        // Update price
        $lineItem->setPrice($product->getPrice());

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($orderCollection);
        $em->persist($order);
        $em->persist($lineItem);
        $em->flush();

        $session->set('cart_id', $orderCollection->getId());

        return $this->redirect($this->generateUrl('Buyer_product_show', array(
            'id'   => $product_id,
            'path' => $product->getCategoryPath(),
        )));
    }
}

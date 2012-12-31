<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\BuyerBundle\Controller;

use HarvestCloud\MarketPlace\BuyerBundle\Controller\BuyerController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use HarvestCloud\CoreBundle\Entity\OrderCollection;
use HarvestCloud\CoreBundle\Entity\Order;
use HarvestCloud\CoreBundle\Entity\OrderLineItem;
use HarvestCloud\CoreBundle\Entity\Product;

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
     *
     * @Route("/cart/add-product/{id}/{quantity}")
     * @ParamConverter("product", class="HarvestCloudCoreBundle:Product")
     *
     * @param  Product  $product
     */
    public function addProductAction(Product $product, $quantity)
    {
        // Find OrderCollection
        $orderCollection = $this->getCurrentCart();

        try
        {
            // Add Product to OrderCollection
            $lineItem = $orderCollection->addProduct($product, $quantity);

            // Persisting cascades to Order and OrderCollection
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($lineItem);
            $em->flush();

            // Save the OrderCollection to the session
            $this->getRequest()->getSession()->set('cart_id', $orderCollection->getId());
        }
        catch (\Exception $e)
        {
            // could not add Product to cart
        }

        return $this->redirect($this->generateUrl('Buyer_product_show', array(
            'id'   => $product->getId(),
            'path' => $product->getCategoryPath(),
        )));
    }
}

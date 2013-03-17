<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\BuyerBundle\Controller;

use HarvestCloud\MarketPlace\CoreBundle\Controller\CoreController as Controller;
use HarvestCloud\CoreBundle\Entity\OrderCollection;

/**
 * BuyerController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2012-04-21
 */
class BuyerController extends Controller
{
    /**
     * getCurrentCart()
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-20
     *
     * @return OrderCollection
     */
    public function getCurrentCart()
    {
        // Grab the session
        $session = $this->getRequest()->getSession();

        // Check if we have a reference to the cart in the session
        if ($session->get('cart_id')) {
            $orderCollection = $this->getRepo('OrderCollection')
                ->find($session->get('cart_id'));

            // Check if a cart really exists
            if ($orderCollection) {
                return $orderCollection;
            }
        }

        // Cannot find existing cart, create a new one
        $orderCollection = new OrderCollection();

        // Persist new so that we get a 'cart_id'
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($orderCollection);
        $em->flush();

        // Save new cart to session
        $session->set('cart_id', $orderCollection->getId());

        return $orderCollection;
    }
}

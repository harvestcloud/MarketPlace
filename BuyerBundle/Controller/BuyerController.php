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
        $session = $this->getRequest()->getSession();
        $buyer   = $this->getCurrentProfile();

        $orderCollection = $this->getRepo('OrderCollection')
            ->find($session->get('cart_id'));

        if (!$orderCollection)
        {
            $orderCollection = new OrderCollection();
            $orderCollection->setBuyer($buyer);
        }

        return $orderCollection;
    }
}

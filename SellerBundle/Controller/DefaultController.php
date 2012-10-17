<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\SellerBundle\Controller;

use HarvestCloud\MarketPlace\SellerBundle\Controller\SellerController as Controller;


class DefaultController extends Controller
{
    public function indexAction()
    {
        if (!$this->getCurrentProfile()->hasActiveSellerStatus())
        {
            return $this->redirect($this->generateUrl('Seller_register_landing'));
        }

        return $this->render('HarvestCloudMarketPlaceSellerBundle:Default:index.html.twig');
    }
}

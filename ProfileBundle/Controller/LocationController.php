<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\ProfileBundle\Controller;

use HarvestCloud\MarketPlace\ProfileBundle\Controller\ProfileController as Controller;

/**
 * LocationController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2012-11-15
 */
class LocationController extends Controller
{
    /**
     * index
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-11-15
     */
    public function indexAction()
    {
        return $this->render('HarvestCloudMarketPlaceProfileBundle:Location:index.html.twig', array(
            'locations' => array(),
        ));
    }
}

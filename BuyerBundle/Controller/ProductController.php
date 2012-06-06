<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\BuyerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use HarvestCloud\CoreBundle\Entity\Product;
use HarvestCloud\CoreBundle\Form\ProductType;

/**
 * ProductController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2012-04-07
 */
class ProductController extends Controller
{
    /**
     * show
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-04-07
     *
     * @param  int  $id  Product::id
     */
    public function showAction($id)
    {
        $product = $this->getDoctrine()
            ->getRepository('HarvestCloudCoreBundle:Product')
            ->find($id);

        if (!$product)
        {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        return $this->render('HarvestCloudMarketPlaceBuyerBundle:Product:show.html.twig', array(
          'product' => $product,
        ));
    }
}

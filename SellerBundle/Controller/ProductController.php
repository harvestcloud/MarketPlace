<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\SellerBundle\Controller;

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

        return $this->render('HarvestCloudMarketPlaceSellerBundle:Product:show.html.twig', array(
          'product' => $product,
        ));
    }

    /**
     * new
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-04-12
     *
     * @param  Request $request
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(new ProductType(), $product);

        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($product);
                $em->flush();

                return $this->redirect($this->generateUrl('Seller_product_show', array(
                    'id' => $product->getId(),
                )));
            }
        }

        return $this->render('HarvestCloudMarketPlaceSellerBundle:Product:new.html.twig', array(
          'form' => $form->createView(),
        ));
    }
}

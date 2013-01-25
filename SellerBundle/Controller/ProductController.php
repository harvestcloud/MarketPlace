<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\SellerBundle\Controller;

use HarvestCloud\MarketPlace\SellerBundle\Controller\SellerController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use HarvestCloud\CoreBundle\Entity\Product;
use HarvestCloud\CoreBundle\Entity\RestockTransaction;
use HarvestCloud\CoreBundle\Form\ProductType;
use HarvestCloud\CoreBundle\Form\RestockTransactionType;

/**
 * ProductController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2012-04-07
 */
class ProductController extends Controller
{
    /**
     * index
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-10
     */
    public function indexAction()
    {
        $products = $this->getRepo('Product')
            ->findOpenForSeller($this->getCurrentProfile())
        ;

        return $this->render('HarvestCloudMarketPlaceSellerBundle:Product:index.html.twig', array(
          'products' => $products,
        ));
    }

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
        $form = $this->createForm(new ProductType($this->getCurrentProfile()), $product);

        // During the Eggbox stage, we'll hard code the Category
        $product->setCategory($this->getDoctrine()
            ->getRepository('HarvestCloudCoreBundle:Category')
            ->find(10));

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

    /**
     * restock
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2013-01-21
     *
     * @Route("/product/{id}/restock")
     * @ParamConverter("product", class="HarvestCloudCoreBundle:Product")
     *
     * @param  Product  $product
     */
    public function restockAction(Product $product, Request $request)
    {
        $restock = new RestockTransaction();
        $restock->setProduct($product);

        $form = $this->createForm(new RestockTransactionType(), $restock);

        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                try
                {
                    // For now, we always post the RestockTransaction, but
                    // later we'll check to see if this is a future transaction
                    $restock->post();

                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($restock);
                    $em->flush();

                    return $this->redirect($this->generateUrl('Seller_product_show', array(
                        'id' => $product->getId(),
                    )));
                }
                catch (\Exception $e)
                {
                    // could not Restock Product
                }
            }
        }

        return $this->render('HarvestCloudMarketPlaceSellerBundle:Product:restock.html.twig', array(
          'form'    => $form->createView(),
          'product' => $product,
        ));
    }
}

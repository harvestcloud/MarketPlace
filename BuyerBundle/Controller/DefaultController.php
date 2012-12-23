<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\BuyerBundle\Controller;

use HarvestCloud\MarketPlace\BuyerBundle\Controller\BuyerController as Controller;
use HarvestCloud\CoreBundle\Entity\ProductFilter;
use HarvestCloud\GeoBundle\Util\LatLng;

/**
 * DefaultController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2012-04-17
 */
class DefaultController extends Controller
{
    /**
     * index
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-04-15
     */
    public function indexAction()
    {
        $currentProfile = $this->getCurrentProfile();

        // Set up and empty filter
        $filter = new ProductFilter();
        $filter->setLatitude($currentProfile->getDefaultLocation()->getLatitude());
        $filter->setLongitude($currentProfile->getDefaultLocation()->getLongitude());
        $filter->setRange(100);

        $products = $this->getRepo('Product')
            ->findForSearchFilter($filter, $this->getCurrentCart())
        ;

        return $this->render('HarvestCloudMarketPlaceBuyerBundle:Default:index.html.twig', array(
          'products' => $products,
        ));
    }

    /**
     * browse
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-04-19
     */
    public function browseAction($path)
    {
        $repository = $this->getDoctrine()->getRepository('HarvestCloudCoreBundle:Category');

        $category = $repository->findOneByPath($path);

        if (!$category)
        {
            throw $this->createNotFoundException('No category found for path '.$path);
        }

        $products = array();

        foreach ($category->getProducts() as $product)
        {
            $products[] = $product;
        }

        foreach ($repository->children($category) as $child)
        {
            foreach ($child->getProducts() as $product)
            {
                $products[] = $product;
            }
        }

        return $this->render('HarvestCloudMarketPlaceBuyerBundle:Default:browse.html.twig', array(
          'products' => $products,
        ));
    }
}

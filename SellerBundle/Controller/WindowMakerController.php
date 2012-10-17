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
use Symfony\Component\HttpFoundation\Request;
use HarvestCloud\CoreBundle\Entity\WindowMaker;
use HarvestCloud\CoreBundle\Form\WindowMakerType;
use HarvestCloud\CoreBundle\Util\Debug;
use Symfony\Component\Form\Form;

/**
 * WindowMakerController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since 2012-10-09
 */
class WindowMakerController extends Controller
{
    /**
     * index
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-10
     */
    public function indexAction()
    {
        $currentProfile = $this->getCurrentProfile();

        $windowMakers = $this->getRepo('WindowMaker')
            ->findForSeller($currentProfile)
        ;

        return $this->render('HarvestCloudMarketPlaceSellerBundle:WindowMaker:index.html.twig', array(
          'windowMakers' => $windowMakers,
        ));
    }

    /**
     * show
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-09
     *
     * @Route("/window-maker/{id}")
     * @ParamConverter("windowMaker", class="HarvestCloudCoreBundle:WindowMaker")
     *
     * @param  WindowMaker  $windowMaker
     */
    public function showAction($windowMaker)
    {
        return $this->render('HarvestCloudMarketPlaceSellerBundle:WindowMaker:show.html.twig', array(
          'windowMaker' => $windowMaker,
        ));
    }

    /**
     * new
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-09
     *
     * @param  Request $request
     */
    public function newAction(Request $request)
    {
        $windowMaker = new WindowMaker();
        $form = $this->createForm(new WindowMakerType($this->getCurrentProfile()), $windowMaker);

        if ($response = $this->processForm($request, $form, 'Seller_window_maker_show'))
        {
            return $response;
        }

        return $this->render('HarvestCloudMarketPlaceSellerBundle:WindowMaker:new.html.twig', array(
          'form' => $form->createView(),
        ));
    }

    /**
     * edit
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-09
     *
     * @Route("/window-maker/{id}")
     * @ParamConverter("windowMaker", class="HarvestCloudCoreBundle:WindowMaker")
     *
     * @param  Request $request
     */
    public function editAction(WindowMaker $windowMaker, Request $request)
    {
        $form = $this->createForm(new WindowMakerType($this->getCurrentProfile()), $windowMaker);

        if ($response = $this->processForm($request, $form, 'Seller_window_maker_show'))
        {
            return $response;
        }

        return $this->render('HarvestCloudMarketPlaceSellerBundle:WindowMaker:edit.html.twig', array(
          'form'        => $form->createView(),
          'windowMaker' => $windowMaker,
        ));
    }
}

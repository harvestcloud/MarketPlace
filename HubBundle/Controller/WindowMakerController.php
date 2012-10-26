<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\HubBundle\Controller;

use HarvestCloud\MarketPlace\HubBundle\Controller\HubController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use HarvestCloud\CoreBundle\Entity\WindowMaker;
use HarvestCloud\CoreBundle\Entity\HubWindowMaker;
use HarvestCloud\CoreBundle\Form\HubWindowMakerType;
use HarvestCloud\CoreBundle\Util\Debug;
use Symfony\Component\Form\Form;

/**
 * WindowMakerController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2012-10-25
 */
class WindowMakerController extends Controller
{
    /**
     * index
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-25
     */
    public function indexAction()
    {
        $currentProfile = $this->getCurrentProfile();

        $windowMakers = $this->getRepo('HubWindowMaker')
            ->findForHub($currentProfile)
        ;

        $slots = $this->getRepo('HubWindowMaker')
            ->getCalendarViewArray($currentProfile);

        return $this->render('HarvestCloudMarketPlaceHubBundle:WindowMaker:index.html.twig', array(
          'windowMakers' => $windowMakers,
          'slots'        => $slots,
        ));
    }

    /**
     * show
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-25
     *
     * @Route("/window-maker/{id}")
     * @ParamConverter("windowMaker", class="HarvestCloudCoreBundle:HubWindowMaker")
     *
     * @param  HubWindowMaker  $windowMaker
     */
    public function showAction($windowMaker)
    {
        return $this->render('HarvestCloudMarketPlaceHubBundle:WindowMaker:show.html.twig', array(
          'windowMaker' => $windowMaker,
        ));
    }

    /**
     * new
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-25
     *
     * @param  Request $request
     */
    public function newAction(Request $request)
    {
        $windowMaker = new HubWindowMaker();
        $windowMaker->setHub($this->getCurrentProfile());
        $form = $this->createForm(new HubWindowMakerType($this->getCurrentProfile()), $windowMaker);

        if ($response = $this->processForm($request, $form, 'Hub_window_maker_show'))
        {
            return $response;
        }

        return $this->render('HarvestCloudMarketPlaceHubBundle:WindowMaker:new.html.twig', array(
          'form' => $form->createView(),
        ));
    }

    /**
     * edit
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-10-25
     *
     * @Route("/window-maker/{id}")
     * @ParamConverter("windowMaker", class="HarvestCloudCoreBundle:HubWindowMaker")
     *
     * @param  Request $request
     */
    public function editAction(HubWindowMaker $windowMaker, Request $request)
    {
        $form = $this->createForm(new HubWindowMakerType($this->getCurrentProfile()), $windowMaker);

        if ($response = $this->processForm($request, $form, 'Hub_window_maker_show'))
        {
            return $response;
        }

        return $this->render('HarvestCloudMarketPlaceHubBundle:WindowMaker:edit.html.twig', array(
          'form'        => $form->createView(),
          'windowMaker' => $windowMaker,
        ));
    }
}

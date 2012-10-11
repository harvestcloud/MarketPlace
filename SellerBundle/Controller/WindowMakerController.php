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
        $form = $this->createForm(new WindowMakerType(), $windowMaker);

        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($windowMaker);
                $em->flush();

                return $this->redirect($this->generateUrl('Seller_window_maker_show', array(
                    'id' => $windowMaker->getId(),
                )));
            }
        }

        return $this->render('HarvestCloudMarketPlaceSellerBundle:WindowMaker:new.html.twig', array(
          'form' => $form->createView(),
        ));
    }
}

<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\ProfileBundle\Controller;

use HarvestCloud\MarketPlace\ProfileBundle\Controller\ProfileController as Controller;
use Symfony\Component\HttpFoundation\Request;
use HarvestCloud\CoreBundle\Form\ProfileType;
use HarvestCloud\DoubleEntryBundle\Entity\Account;

/**
 * AccountController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2013-02-04
 */
class AccountController extends Controller
{
    /**
     * index
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2013-02-06
     */
    public function indexAction()
    {
        return $this->render('HarvestCloudMarketPlaceProfileBundle:Account:index.html.twig', array(
            'profile' => $this->getCurrentProfile(),
        ));
    }

    /**
     * show
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2013-02-06
     */
    public function showAction($slug)
    {
        $account = $this
            ->getDoctrine()
            ->getRepository('HarvestCloudDoubleEntryBundle:Account')
            ->findOneBy(array(
                'profile' => $this->getCurrentProfile(),
                'slug'    => $slug
            ))
        ;

        $postings = $this
            ->getDoctrine()
            ->getRepository('HarvestCloudDoubleEntryBundle:Account')
            ->findPostingsForAccount($account)
        ;

        return $this->render('HarvestCloudMarketPlaceProfileBundle:Account:show.html.twig', array(
            'profile'  => $this->getCurrentProfile(),
            'account'  => $account,
            'postings' => $postings,
        ));
    }

    /**
     * create_set
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2013-02-04
     *
     * @param  Request $request
     */
    public function create_setAction(Request $request)
    {
        try
        {
            $this->getCurrentProfile()->createSetOfAccounts();

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($this->getCurrentProfile());
            $em->flush();
        }
        catch (\Exception $e)
        {
        }

        return $this->redirect($this->generateUrl('Profile_homepage'));
    }
}

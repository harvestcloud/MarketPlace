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

/**
 * AccountController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2013-02-04
 */
class AccountController extends Controller
{
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

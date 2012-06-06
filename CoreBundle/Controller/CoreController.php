<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * CoreController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2012-04-28
 */
class CoreController extends Controller
{
    /**
     * Get repository for Model Class
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-04-25
     *
     * @param  string  $entity_name
     *
     * @return Doctrine\ORM\EntityRepository
     */
    protected function getRepo($entity_name)
    {
        return $this->getDoctrine()
            ->getRepository('HarvestCloudCoreBundle:'.$entity_name);
    }

    /**
     * Get User
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-04-25
     *
     * @return HarvestCloud\CoreBundle\Entity\User
     */
    protected function getUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }

    /**
     * Get current Profile
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-05-02
     * @todo   Make sure we always have a current profile
     *
     * @return HarvestCloud\CoreBundle\Entity\Profile
     */
    protected function getCurrentProfile()
    {
        return $this->getUser()->getCurrentProfile();
    }
}

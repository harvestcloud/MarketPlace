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
use HarvestCloud\NotifierBundle\Events\OrderAcceptedBySellerEvent;
use HarvestCloud\CoreBundle\Entity\Order;
use HarvestCloud\CoreBundle\Entity\Profile;

/**
 * OrderController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2012-05-11
 */
class OrderController extends Controller
{
    /**
     * index
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-05-11
     */
    public function indexAction()
    {
        $seller = $this->getCurrentProfile();

        $orders = $this->getRepo('Order')
            ->findOpenForSeller($seller)
        ;

        return $this->render('HarvestCloudMarketPlaceSellerBundle:Order:index.html.twig', array(
          'orders' => $orders,
        ));
    }

    /**
     * accept
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-05-13
     *
     * @Route("/order/{id}/accept")
     * @ParamConverter("order", class="HarvestCloudCoreBundle:Order")
     *
     * @param  Order  $order
     */
    public function acceptAction(Order $order)
    {
        $order->acceptBySeller();

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($order);
        $em->flush();

        $this->get('notifier')->notify(new OrderAcceptedBySellerEvent($order), $this->getUser());

        return $this->redirect($this->generateUrl('Seller_order'));
    }

    /**
     * reject
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-05-14
     *
     * @Route("/order/{id}/reject")
     * @ParamConverter("order", class="HarvestCloudCoreBundle:Order")
     *
     * @param  Order  $order
     */
    public function rejectAction(Order $order)
    {
        $order->rejectBySeller();

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($order);
        $em->flush();

        return $this->redirect($this->generateUrl('Seller_order'));
    }

    /**
     * pick
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-05-14
     *
     * @Route("/order/{id}/pick")
     * @ParamConverter("order", class="HarvestCloudCoreBundle:Order")
     *
     * @param  Order  $order
     */
    public function pickAction(Order $order)
    {
        $order->pickBySeller();

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($order);
        $em->flush();

        return $this->redirect($this->generateUrl('Seller_order'));
    }

    /**
     * mark ready for dispatch
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-05-14
     *
     * @Route("/order/{id}/mark-ready-for-dispatch")
     * @ParamConverter("order", class="HarvestCloudCoreBundle:Order")
     *
     * @param  Order  $order
     */
    public function mark_ready_for_dispatchAction(Order $order)
    {
        $order->markReadyForDispatchBySeller();

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($order);
        $em->flush();

        return $this->redirect($this->generateUrl('Seller_order'));
    }

    /**
     * dispatch
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2012-05-14
     * @todo   Use better method to getAmountForPaymentGateway()
     *
     * @Route("/order/{id}/dispatch")
     * @ParamConverter("order", class="HarvestCloudCoreBundle:Order")
     *
     * @param  Order  $order
     */
    public function dispatchAction(Order $order)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $order->dispatchBySeller();

        // Create invoice
        $invoice = new \HarvestCloud\InvoiceBundle\Entity\OrderInvoice();
        $invoice->setAmount($order->getAmountForPaymentGateway());
        $em->persist($invoice);

        $message = \Swift_Message::newInstance()
            ->setSubject('Your order has been dispatched')
            ->setFrom(array('no-reply@harvestcloud.com' => 'Harvest Cloud'))
            ->setTo(array('buyer@example.com' => 'Buyer Name'))
            ->setBody($this->renderView(
                'HarvestCloudEmailBundle:Buyer:order_dispatched_by_seller.txt.twig', array(
                    'order' => $order
            )))
        ;

        $this->get('mailer')->send($message);

        $em->persist($order);
        $em->flush();

        return $this->redirect($this->generateUrl('Seller_order'));
    }
}

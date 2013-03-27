<?php

/*
 * This file is part of the Harvest Cloud package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HarvestCloud\MarketPlace\BuyerBundle\Controller;

use HarvestCloud\MarketPlace\BuyerBundle\Controller\BuyerController as Controller;
use HarvestCloud\InvoiceBundle\Entity\Invoice;
use HarvestCloud\PaymentBundle\Entity\InvoicePayment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * InvoiceController
 *
 * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
 * @since  2013-03-23
 */
class InvoiceController extends Controller
{
    /**
     * index
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2013-03-23
     */
    public function indexAction()
    {
        $buyer = $this->get('security.context')
            ->getToken()
            ->getUser()
            ->getCurrentProfile()
        ;

        $invoices = $this->get('doctrine')
            ->getRepository('HarvestCloudInvoiceBundle:Invoice')
            ->findByCustomer($buyer)
        ;

        return $this->render('HarvestCloudMarketPlaceBuyerBundle:Invoice:index.html.twig', array(
          'invoices' => $invoices,
        ));
    }

    /**
     * show
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2013-03-23
     *
     * @Route("/invoice/{id}")
     * @ParamConverter("invoice", class="HarvestCloudInvoiceBundle:Invoice")
     *
     * @param  Invoice  $invoice
     */
    public function showAction(Invoice $invoice, Request $request)
    {
        $buyer = $this->get('security.context')
            ->getToken()
            ->getUser()
            ->getCurrentProfile()
        ;

        // $savedCreditCards = $buyer->getSavedCreditCards();

        return $this->render('HarvestCloudMarketPlaceBuyerBundle:Invoice:show.html.twig', array(
            'invoice' => $invoice,
        ));
    }

    /**
     * payWithStripe
     *
     * @author Tom Haskins-Vaughan <tom@harvestcloud.com>
     * @since  2013-03-24
     *
     * @Route("/invoice/pay/stripe/{id}")
     * @ParamConverter("invoice", class="HarvestCloudInvoiceBundle:Invoice")
     *
     * @param  Invoice  $invoice
     */
    public function payWithStripeAction(Invoice $invoice, Request $request)
    {
        $buyer = $this->get('security.context')
            ->getToken()
            ->getUser()
            ->getCurrentProfile()
        ;

        if ($buyer !== $invoice->getCustomer()) {
            throw new NotFoundHttpException('Buyer is not Invoice Customer');
        }

        // First we need to create a payment
        $payment = new InvoicePayment(array($invoice));

        // Save the Payment, so that we get an id
        $em = $this->get('doctrine')->getEntityManager();
        $em->persist($payment);
        $em->flush();

        $this->get('payment.stripe');

        // Grap Stripe token
        $token  = $_POST['stripeToken'];

        try {
            $charge = \Stripe_Charge::create(array(
                'card'        => $token,
                'amount'      => floor($payment->getAmount()*100),
                'currency'    => 'usd',
                'description' => 'Invoice Payment #'.$payment->getId(),
            ));

            // Everything went OK, so let's post the Payment
            $payment->post();
        } catch (\Stripe_CardError $e) {
            // @todo Send some messages back to the browser
            // for now, we'll just re-throw the exception
            throw $e;
        }

        // Save the Paymenti again after the post()
        $em->persist($payment);
        $em->flush();

        return $this->redirect($this->generateurl('Buyer_invoice_show', array(
            'id' => $invoice->getId(),
        )));
    }
}

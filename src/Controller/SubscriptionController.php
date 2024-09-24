<?php

namespace App\Controller;

use App\Service\EmailNotificationService;
use Stripe\Webhook;
use App\Service\PaymentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class SubscriptionController extends AbstractController
{
    // Route lorsque le paiement est réussi
    #[Route('/payment-success', name: 'app_payment_success', methods: ['GET'])]
    public function paymentSuccess(Request $request, PaymentService $ps, EmailNotificationService $ens): Response
    {
        if($request->headers->get('referer') === 'https://checkout.stripe.com/' )
        {
            $subscription = $ps->addSubscription();
            $ens->sendEmail(
                $this->getUser()->getEmail(),
                [
                    'subject' => 'Thank you for your purchase!',
                    'template' => 'premium',
                ]
            );
            return $this->render('subscription/payment-success.html.twig', [
                'subscription' => $subscription,
            ]);
        } else {
            $this->addFlash('error', "You can't take a subscription without a payment");
            return $this->redirectToRoute('app_subscription');
        }

        return $this->render('subscription/payment-success.html.twig');
    }

    // Route lorsque le paiement a échoué
    #[Route('/payment-cancel', name: 'app_payment_cancel')]
    public function paymentCancel(Request $request): Response
    {
        if ($request->headers->get('referer') === 'https://checkout.stripe.com/' )
        {
            return $this->render('subscription/payment-cancel.html.twig');
        } else {
            $this->addFlash('error', "You can't take a subscription without a payment");
            return $this->redirectToRoute('app_subscription');
        }
    }

    // Page de présentation de l'abonnement Premium
    #[Route('/subscription/', name: 'app_subscription', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('subscription/index.html.twig');
    }

    /**
     * Redirection vers le paiement Stripe
     * Ici on utilise la classe RedirectResponse de HttpFoundation
     * Cela nous donne accès à la méthos redirect qui génère la requête
     * à partir de la session initié avec PaymentService->askCheckout()
     **/
    #[Route('/subscription/checkout', name: 'app_subscription_checkout', methods: ['GET'])]
    public function checkout(PaymentService $ps): RedirectResponse
    {
        return $this->redirect($ps->askCheckout()->url);
    }

    /**
     * Check du statut de paiement
     */
    #[Route('/payment-webhook', name: 'app_stripe_webhook', methods: ['GET', 'POST'])]
    public function stripeWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $endpointSecret = $this->getParameter('stripe_webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
            dd($event);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }
    }
}

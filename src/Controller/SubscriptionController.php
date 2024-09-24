<?php

namespace App\Controller;

use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SubscriptionController extends AbstractController
{
    // Route lorsque le paiement est réussi
    #[Route('/payment-success', name: 'app_payment_success', methods: ['GET'])]
    public function paymentSuccess(Request $request): Response
    {
        dd($request);
        return $this->render('subscription/payment-success.html.twig');
    }

    // Route lorsque le paiement a échoué
    #[Route('/payment-cancel', name: 'app_payment_cancel')]
    public function paymentCancel(): Response
    {
        return $this->render('subscription/payment-cancel.html.twig');
    }

    // Page de présentation de l'abonnement Premium
    #[Route('/subscription', name: 'app_subscription', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('subscription/index.html.twig');
    }

    /* Redirection vers le paiement Stripe
    * Ici on utilise la classe RecdirectResponse de HttpFoundation
    * Cela nous donne accès à la méthos redirect qui génère la requête
    * à partir de la session initié avec PaymentService->askCheckout()
    **/
    #[Route('/subscription/checkout', name: 'app_subscription_checkout', methods: ['GET'])]
    public function checkout(PaymentService $ps): RedirectResponse
    {
        return $this->redirect($ps->askCheckout()->url);
    }
}

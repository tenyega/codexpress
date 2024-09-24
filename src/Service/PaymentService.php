<?php

namespace App\Service;

use Stripe\Stripe;
use App\Repository\OfferRepository;
use App\Service\AbstractService;
use Stripe\Checkout\Session;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class PaymentService extends AbstractService
{
    // private string $apiKey = $this->parametmer->get('STRIPE_API_SK');
    private $offer;
    private $domain;
    private $apiKey;

    public function __construct(OfferRepository $or, ParameterBagInterface $parameter)
    {
        $this->parameter = $parameter;
        // $this->stripe = $stripe; //Creating object of Stripe Class
        $this->offer = $or->findOneByName('Premium');
        $this->apiKey = $this->parameter->get('STRIPE_API_SK');
        $this->domain = 'https://127.0.0.1:8000/en';
    }

    //generate une demande de paiement vers stripe. 
    /**
     * askCheckout()
     * Méthode permettant de créer une session de paiement Stripe
     * @return Stripe\Checkout\Session
     */
    public function askCheckout(): ?Session
    {
        Stripe::setApiKey($this->apiKey); // Établissement de la connexion (requête API)        
        $checkoutSession = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'tax_behavior' => 'exclusive',
                    'unit_amount' => $this->offer->getPrice() * 100, // Stripe utilise des centimes
                    'product_data' => [ // Les informations du produit sont personnalisables
                        'name' => $this->offer->getName(),
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->domain . '/payment-success',
            'cancel_url' => $this->domain . '/payment-cancel',
            'automatic_tax' => [
                'enabled' => true,
            ],
        ]);

        return $checkoutSession;
    }
    //traitement du role de utilisateurs en fonction du paiement. 
    //Generation de la facture 
    //notification par email

}

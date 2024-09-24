<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

abstract class AbstractService
{

    // this class is created by us for all the new command to create a service. 
    public function __construct(
        protected ParameterBagInterface $parameter,
        protected MailerInterface $mailer
    ) {
        $this->parameter = $parameter;
        $this->mailer = $mailer;
    }
}

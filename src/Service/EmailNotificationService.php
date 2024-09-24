<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationService

{
    public function __construct(private MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public function sendEmail(string $receiver, array $case): ?string
    {
        try {
            // $email = (new Email())
            //     ->from('hello@codexpress.fr')
            //     ->to($receiver)
            //     //->cc('cc@example.com')
            //     //->bcc('bcc@example.com')
            //     //->replyTo('fabien@example.com')
            //     //->priority(Email::PRIORITY_HIGH)
            //     ->subject('Time for Symfony Mailer! to '.$receiver)
            //     ->text('Sending emails is fun again!')
            //     ->html('<p>See Twig integration for better HTML integration!</p>');
            $email = (new TemplatedEmail())
                ->from('hello@codexpress.fr')
                ->to($receiver)
                ->subject($case['subject'])
                ->priority(Email::PRIORITY_HIGH)
                ->htmlTemplate('email/' . $case['template'] . '.html.twig' ?? 'email/base_email.html.twig');
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')


            $this->mailer->send($email);

            return 'The email is successfully sent';
        } catch (\Exception $e) {
            return 'An error has occured while sending the email : ' . $e->getMessage();
        }
    }
}

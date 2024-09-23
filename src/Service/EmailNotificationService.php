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
    public function sendEmail(string $receiver, string $case): ?string
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
                ->to($receiver);
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')



            if ($case === 'premium') {
                $email->priority(Email::PRIORITY_HIGH)
                    ->subject('Thank you for your purchase! ')
                    ->htmlTemplate('email/premium.html.twig')
                ;
            } elseif ($case = 'registration') {
                $email->subject('Welcome to codeXpress, explore the world of coding with us. !!! ')
                    ->htmlTemplate('email/welcome.html.twig');
            } else {
                $email->htmlTemplate('email/base_email.html.twig');
            }


            $this->mailer->send($email);

            return 'The email is successfully sent';
        } catch (\Exception $e) {
            return 'An error has occured while sending the email : ' . $e->getMessage();
        }
    }
}

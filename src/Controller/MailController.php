<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class MailController extends AbstractController
{
    #[Route('/mail/test', name: 'app_mail_test')]
    public function sendTestEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('no-reply@starshop.com')
            ->to('user@example.com')
            ->subject('Welcome to Universal Travel!')
            ->text('Thank you for booking your galactic journey with us!')
            ->html('<h1>Welcome to Universal Travel!</h1><p>Thank you for booking your <strong>galactic journey</strong> with us!</p>');

        $mailer->send($email);

        return new Response('<h1>Email Sent Successfully!</h1><p>Check your email preview in the profiler or your configured mailer.</p>');
    }

    #[Route('/mail', name: 'app_mail_home')]
    public function index(): Response
    {
        return $this->render('mail/index.html.twig');
    }
}

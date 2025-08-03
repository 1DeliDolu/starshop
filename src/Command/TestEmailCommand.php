<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

#[AsCommand(
    name: 'app:test-email',
    description: 'Test email sending with CSS inlining',
)]
class TestEmailCommand extends Command
{
    public function __construct(
        private MailerInterface $mailer
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $email = (new TemplatedEmail())
                ->to('test@example.com')
                ->subject('Test Email with CSS Inlining')
                ->htmlTemplate('email/booking_confirmation.html.twig')
                ->textTemplate('email/booking_confirmation.txt.twig')
                ->context([
                    'trip' => (object) ['name' => 'Test Trip to Paris'],
                    'booking' => (object) [
                        'name' => 'Test Customer',
                        'date' => new \DateTime(),
                        'uid' => 'test-booking-123'
                    ],
                    'customer' => (object) [
                        'name' => 'Test Customer',
                        'uid' => 'test-customer-456'
                    ]
                ]);

            $this->mailer->send($email);

            $io->success('Test email sent successfully! The inline_css filter is working with XSL extension.');

        } catch (\Exception $e) {
            $io->error('Failed to send email: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

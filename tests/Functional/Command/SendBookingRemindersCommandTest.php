<?php

namespace App\Tests\Functional\Command;

use App\Factory\BookingFactory;
use App\Factory\CustomerFactory;
use App\Factory\TripFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SendBookingRemindersCommandTest extends KernelTestCase
{
    use ResetDatabase, Factories, MailerAssertionsTrait;

    public function testNoRemindersSent()
    {
        $kernel = static::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:send-booking-reminders');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('Sent 0 booking reminders', $commandTester->getDisplay());
    }

    public function testRemindersSent()
    {
        $booking = BookingFactory::createOne([
            'trip' => TripFactory::new([
                'name' => 'Visit Mars',
                'slug' => 'iss',
            ]),
            'customer' => CustomerFactory::new(['email' => 'steve@minecraft.com']),
            'date' => new \DateTimeImmutable('+4 days'),
        ]);

        $this->assertNull($booking->getReminderSentAt());

        $kernel = static::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:send-booking-reminders');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('Sent 1 booking reminders', $commandTester->getDisplay());

        // Assert email was sent
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailHeaderSame($email, 'to', 'steve@minecraft.com');
        $this->assertEmailHeaderSame($email, 'subject', 'Booking Reminder for Visit Mars');
        $this->assertEmailHtmlBodyContains($email, 'Visit Mars');
        $this->assertEmailHtmlBodyContains($email, '/booking/' . $booking->getUid());

        $this->assertNotNull($booking->getReminderSentAt());
    }
}

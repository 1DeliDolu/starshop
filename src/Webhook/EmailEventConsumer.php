<?php

namespace App\Webhook;

use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\Event\Mailer\MailerDeliveryEvent;
use Symfony\Component\RemoteEvent\Event\Mailer\MailerEngagementEvent;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('mailtrap')]
class EmailEventConsumer implements ConsumerInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    /**
     * @param MailerDeliveryEvent|MailerEngagementEvent $event
     */
    public function consume(RemoteEvent $event): void
    {
        // Dump the event for debugging
        dump($event);

        // Log the event for debugging
        $this->logger->info('Received email event from Mailtrap', [
            'event_type' => get_class($event),
            'event_name' => $event->getName(),
            'date' => $event->getDate()?->format('Y-m-d H:i:s'),
            'recipient_email' => $event->getRecipientEmail(),
            'payload' => $event->getPayload(),
        ]);

        // Handle different types of email events
        if ($event instanceof MailerDeliveryEvent) {
            $this->handleDeliveryEvent($event);
        } elseif ($event instanceof MailerEngagementEvent) {
            $this->handleEngagementEvent($event);
        }
    }

    private function handleDeliveryEvent(MailerDeliveryEvent $event): void
    {
        $eventName = $event->getName();

        match ($eventName) {
            'delivered' => $this->handleDelivered($event),
            'bounce' => $this->handleBounced($event),
            'complaint' => $this->handleComplaint($event),
            'delivery_delay' => $this->handleDeliveryDelay($event),
            'reject' => $this->handleReject($event),
            default => $this->logger->warning('Unknown delivery event type', [
                'event_name' => $eventName,
                'recipient' => $event->getRecipientEmail(),
            ]),
        };
    }

    private function handleEngagementEvent(MailerEngagementEvent $event): void
    {
        $eventName = $event->getName();

        match ($eventName) {
            'open' => $this->handleOpened($event),
            'click' => $this->handleClicked($event),
            'unsubscribe' => $this->handleUnsubscribed($event),
            'spam' => $this->handleSpam($event),
            default => $this->logger->warning('Unknown engagement event type', [
                'event_name' => $eventName,
                'recipient' => $event->getRecipientEmail(),
            ]),
        };
    }

    private function handleDelivered(MailerDeliveryEvent $event): void
    {
        $this->logger->info('Email delivered successfully', [
            'recipient' => $event->getRecipientEmail(),
            'tags' => $event->getTags(),
        ]);

        // Here you could update database records, send notifications, etc.
        // For example: mark booking confirmation as delivered
    }

    private function handleBounced(MailerDeliveryEvent $event): void
    {
        $this->logger->warning('Email bounced', [
            'recipient' => $event->getRecipientEmail(),
            'reason' => $event->getReason(),
            'tags' => $event->getTags(),
        ]);

        // Here you could mark email addresses as invalid, send alerts, etc.
    }

    private function handleComplaint(MailerDeliveryEvent $event): void
    {
        $this->logger->warning('Email complaint received', [
            'recipient' => $event->getRecipientEmail(),
            'reason' => $event->getReason(),
            'tags' => $event->getTags(),
        ]);

        // Here you could handle spam complaints
    }

    private function handleDeliveryDelay(MailerDeliveryEvent $event): void
    {
        $this->logger->info('Email delivery delayed', [
            'recipient' => $event->getRecipientEmail(),
            'reason' => $event->getReason(),
            'tags' => $event->getTags(),
        ]);
    }

    private function handleReject(MailerDeliveryEvent $event): void
    {
        $this->logger->error('Email rejected', [
            'recipient' => $event->getRecipientEmail(),
            'reason' => $event->getReason(),
            'tags' => $event->getTags(),
        ]);
    }

    private function handleOpened(MailerEngagementEvent $event): void
    {
        $this->logger->info('Email opened', [
            'recipient' => $event->getRecipientEmail(),
            'tags' => $event->getTags(),
        ]);

        // Here you could track open rates, update engagement metrics, etc.
    }

    private function handleClicked(MailerEngagementEvent $event): void
    {
        $this->logger->info('Email link clicked', [
            'recipient' => $event->getRecipientEmail(),
            'tags' => $event->getTags(),
        ]);

        // Here you could track engagement, update analytics, etc.
    }

    private function handleUnsubscribed(MailerEngagementEvent $event): void
    {
        $this->logger->info('User unsubscribed', [
            'recipient' => $event->getRecipientEmail(),
            'tags' => $event->getTags(),
        ]);

        // Here you could update user preferences, remove from lists, etc.
    }

    private function handleSpam(MailerEngagementEvent $event): void
    {
        $this->logger->warning('Email marked as spam', [
            'recipient' => $event->getRecipientEmail(),
            'tags' => $event->getTags(),
        ]);

        // Here you could analyze content, adjust sending practices, etc.
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zenstruck\MessengerMonitorBundle\Entity\BaseProcessedMessage;

#[ORM\Entity]
#[ORM\Table(name: 'processed_message')]
class ProcessedMessage extends BaseProcessedMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}

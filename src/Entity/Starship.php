<?php

namespace App\Entity;

use App\Model\StarshipStatusEnum;
use App\Repository\StarshipRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StarshipRepository::class)]
class Starship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column()]
    private ?string $name = null;

    #[ORM\Column()]
    private ?string $class = null;

    #[ORM\Column()]
    private ?string $captain = null;

    #[ORM\Column()]
    private ?StarshipStatusEnum $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $arriveAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): static
    {
        $this->class = $class;

        return $this;
    }

    public function getCaptain(): ?string
    {
        return $this->captain;
    }

    public function setCaptain(string $captain): static
    {
        $this->captain = $captain;

        return $this;
    }

    public function getStatus(): ?StarshipStatusEnum
    {
        return $this->status;
    }

    public function getStatusImageFilename(): string
    {
        return match ($this->status) {
            StarshipStatusEnum::WAITING => 'images/status-waiting.png',
            StarshipStatusEnum::IN_PROGRESS => 'images/status-in-progress.png',
            StarshipStatusEnum::COMPLETED => 'images/status-complete.png',
        };
    }

    public function getArriveAt(): ?\DateTimeImmutable
    {
        return $this->arriveAt;
    }

    public function setArriveAt(\DateTimeImmutable $arriveAt): static
    {
        $this->arriveAt = $arriveAt;

        return $this;
    }
}

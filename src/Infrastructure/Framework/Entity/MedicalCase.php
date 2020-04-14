<?php

namespace App\Infrastructure\Framework\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ChangeTrackingPolicy;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Framework\Repository\MedicalCaseRepository")
 * @ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class MedicalCase
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private string $description;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private string $treatment;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $startedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $endedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $ended;

    /**
     * @ORM\ManyToOne(targetEntity="App\Infrastructure\Framework\Entity\Card", inversedBy="cases")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?Card $card;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $cardId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $value): self
    {
        $this->id = $value;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTreatment(): ?string
    {
        return $this->treatment;
    }

    public function setTreatment(string $treatment): self
    {
        $this->treatment = $treatment;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(\DateTimeInterface $endedAt): self
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function getEnded(): ?bool
    {
        return $this->ended;
    }

    public function setEnded(bool $ended): self
    {
        $this->ended = $ended;

        return $this;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function setCard(?Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function getCardId(): ?int
    {
        return $this->cardId;
    }

    public function setCardId(int $cardId): self
    {
        $this->cardId = $cardId;

        return $this;
    }
}

<?php

namespace App\Infrastructure\Framework\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Infrastructure\Framework\Entity\Card;
use Doctrine\ORM\Mapping\ChangeTrackingPolicy;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Framework\Entity\PatientRepository")
 * @ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Patient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private string $name;

    /**
     * @ORM\Column(type="date")
     */
    private DateTimeInterface $birthDate;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private string $species;

    /**
     * @ORM\ManyToOne(targetEntity="App\Infrastructure\Framework\Entity\Owner", inversedBy="patients", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Owner $owner = null;

    /**
     * @var Collection<mixed,Card>
     * @ORM\OneToMany(targetEntity="App\Infrastructure\Framework\Entity\Card", mappedBy="patient", fetch="EAGER")
     */
    private Collection $cards;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $ownerId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $value): self
    {
        $this->id = $value;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getSpecies(): ?string
    {
        return $this->species;
    }

    public function setSpecies(string $species): self
    {
        $this->species = $species;

        return $this;
    }

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function setOwner(?Owner $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<mixed,Card>|Card[]
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setPatient($this);
        }

        return $this;
    }

    /**
     * Sets cards equal to the given list
     *
     * @param Collection<mixed,Card>|Card[] $cards
     * @return self
     */
    public function setCards(Collection $cards): self
    {
        $this->cards = $cards;

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->contains($card)) {
            $this->cards->removeElement($card);
            // set the owning side to null (unless already changed)
            if ($card->getPatient() === $this) {
                $card->setPatient(null);
            }
        }

        return $this;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function setOwnerId(?int $ownerId): self
    {
        $this->ownerId = $ownerId;

        return $this;
    }
}

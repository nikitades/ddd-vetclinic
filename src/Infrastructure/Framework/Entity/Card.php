<?php

namespace App\Infrastructure\Framework\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ChangeTrackingPolicy;
use Doctrine\Common\Collections\ArrayCollection;
use App\Infrastructure\Framework\Entity\MedicalCase;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Framework\Repository\CardRepository")
 * @ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Card
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Infrastructure\Framework\Entity\Patient", inversedBy="cards")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private ?Patient $patient;

    /**
     * @var Collection<mixed,MedicalCase>|MedicalCase[]
     * @ORM\OneToMany(targetEntity="App\Infrastructure\Framework\Entity\MedicalCase", mappedBy="card", fetch="EAGER")
     */
    private Collection $cases;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $patientId;

    public function __construct()
    {
        $this->cases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $value): self
    {
        $this->id = $value;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * @return Collection<mixed,MedicalCase>|MedicalCase[]
     */
    public function getCases(): Collection
    {
        return $this->cases;
    }

    public function addCase(MedicalCase $case): self
    {
        if (!$this->cases->contains($case)) {
            $this->cases[] = $case;
            $case->setCard($this);
        }

        return $this;
    }

    /**
     * Sets the cases list equal to the given one
     *
     * @param Collection<mixed,MedicalCase>|MedicalCase[] $cases
     * @return self
     */
    public function setCases(Collection $cases): self
    {
        $this->cases = $cases;

        return $this;
    }

    public function removeCase(MedicalCase $case): self
    {
        if ($this->cases->contains($case)) {
            $this->cases->removeElement($case);
            // set the owning side to null (unless already changed)
            if ($case->getCard() === $this) {
                $case->setCard(null);
            }
        }

        return $this;
    }

    public function getPatientId(): ?int
    {
        return $this->patientId;
    }

    public function setPatientId(int $patientId): self
    {
        $this->patientId = $patientId;

        return $this;
    }
}

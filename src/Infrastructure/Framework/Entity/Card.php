<?php

namespace App\Infrastructure\Framework\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Infrastructure\Framework\Entity\MedicalCase;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Framework\Repository\CardRepository")
 */
class Card
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $closed;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Infrastructure\Framework\Entity\Patient", inversedBy="cards")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\OneToMany(targetEntity="App\Infrastructure\Framework\Entity\MedicalCase", mappedBy="card")
     */
    private $cases;

    public function __construct()
    {
        $this->cases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

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
     * @return Collection|MedicalCase[]
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
}

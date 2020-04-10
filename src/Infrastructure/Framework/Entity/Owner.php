<?php

namespace App\Infrastructure\Framework\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Infrastructure\Framework\Entity\Patient;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Framework\Repository\OwnerRepository")
 */
class Owner
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
     * @ORM\Column(type="string", length=30)
     */
    private string $phone;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private string $address;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $registeredAt;

    /**
     * @var Collection<mixed,Patient>|Patient[]
     * @ORM\OneToMany(targetEntity="App\Infrastructure\Framework\Entity\Patient", mappedBy="owner")
     */
    private Collection $patients;

    public function __construct()
    {
        $this->patients = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getRegisteredAt(): ?\DateTimeInterface
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(\DateTimeInterface $registeredAt): self
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    /**
     * @return Collection<mixed,Patient>|Patient[]
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    public function addPatient(Patient $patient): self
    {
        if (!$this->patients->contains($patient)) {
            $this->patients[] = $patient;
            $patient->setOwner($this);
        }

        return $this;
    }

    /**
     * Sets patients equal to the given list
     *
     * @param Collection<mixed,Patient>|Patient[] $patients
     * @return self
     */
    public function setPatients(Collection $patients): self
    {
        $this->patients = $patients;

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patients->contains($patient)) {
            $this->patients->removeElement($patient);
            // set the owning side to null (unless already changed)
            if ($patient->getOwner() === $this) {
                $patient->setOwner(null);
            }
        }

        return $this;
    }
}

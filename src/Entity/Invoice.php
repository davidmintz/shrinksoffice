<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $payer = null;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'invoice')]
    private Collection $services;

    #[ORM\Column(length: 200)]
    private ?string $internal_notes = null;

    /**
     * @var Collection<int, Credit>
     */
    #[ORM\ManyToMany(targetEntity: Credit::class, mappedBy: 'invoices')]
    private Collection $credits;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->credits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getPayer(): ?Person
    {
        return $this->payer;
    }

    public function setPayer(?Person $payer): static
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setInvoice($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getInvoice() === $this) {
                $service->setInvoice(null);
            }
        }

        return $this;
    }

    public function getInternalNotes(): ?string
    {
        return $this->internal_notes;
    }

    public function setInternalNotes(string $internal_notes): static
    {
        $this->internal_notes = $internal_notes;

        return $this;
    }

    /**
     * @return Collection<int, Credit>
     */
    public function getCredits(): Collection
    {
        return $this->credits;
    }

    public function addCredit(Credit $credit): static
    {
        if (!$this->credits->contains($credit)) {
            $this->credits->add($credit);
            $credit->addInvoice($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): static
    {
        if ($this->credits->removeElement($credit)) {
            $credit->removeInvoice($this);
        }

        return $this;
    }

    public function getTotal() : int
    {
        $total = 0;
        foreach ($this->getCredits() as $credit) {
            $total += $credit->getAmount();
        }

        return $total;
    }
}

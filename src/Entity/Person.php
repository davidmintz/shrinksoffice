<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[UniqueConstraint(name: "unique_email", columns: ["email"])]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'last name is required', normalizer: 'trim')]
    private ?string $lastname = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'first name is required', normalizer: 'trim')]
    private ?string $firstname = null;

    #[ORM\Column(length: 30, options: ["default" => ''])]
    private ?string $middlename = '';

    #[ORM\Column(length: 10)]
    private ?string $alias = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank(message: 'email is required', normalizer: 'trim')]
    #[Assert\Email(message: 'invalid email address', normalizer: 'trim')]
    private ?string $email = null;

    #[ORM\Column(type: Types::SMALLINT, options: ["unsigned" => true])]
    private ?int $fee = null;

    #[ORM\Column(length: 400)]
    private ?string $notes = null;

    #[ORM\Column(length: 12)]
    #[Assert\NotBlank(message: 'phone number is required', normalizer: 'trim')]
    private ?string $phone = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'address is required')]
    private ?string $address = null;

    #[ORM\Column(length: 50)]
    private ?string $secondary_address = null;

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank(message: 'city is required', normalizer: 'trim')]
    private ?string $city = null;

    #[ORM\Column(length: 2)]
    #[Assert\NotBlank(message: 'state is required', normalizer: 'trim')]
    #[Assert\Regex(pattern: '/^[A-Z]{2}$/', message: 'two-letter state postal code is required')]
    private ?string $state = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: 'zip code is required', normalizer: 'trim')]
    #[Assert\Regex(pattern: '/^[0-9]{5}(-\d{4})?$/', message: 'invalid zip code')]
    private ?string $postal_code = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'active/inactive is required')]
    private ?bool $active = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $payer = null;

    #[ORM\Column(type: 'string', enumType: PersonType::class)]
    #[Assert\NotBlank(message: 'either "patient" or "payer only" is required')]
    private ?PersonType $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    public function setMiddlename(string $middlename): static
    {
        $this->middlename = $middlename;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }


    public function getFee(): ?int
    {
        return $this->fee;
    }

    public function setFee(int $fee): static
    {
        $this->fee = $fee;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getSecondaryAddress(): ?string
    {
        return $this->secondary_address;
    }

    public function setSecondaryAddress(string $secondary_address): static
    {
        $this->secondary_address = $secondary_address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getPayer(): ?self
    {
        return $this->payer;
    }

    public function setPayer(?self $payer): static
    {
        $this->payer = $payer;

        return $this;
    }

    public function getType(): ?PersonType
    {
        return $this->type;
    }

    public function setType(PersonType $type): static
    {
        $this->type = $type;

        return $this;
    }
}

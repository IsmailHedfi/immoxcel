<?php

namespace App\Entity;

use App\Repository\ExpensesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExpensesRepository::class)]
class Expenses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateE = null;

    #[ORM\Column(length: 255)]
    private ?string $Type = null;

    #[ORM\Column]
    private ?float $QuantityE = null;

    #[ORM\Column]
    private ?float $coast = null;

    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    #[ORM\Column]
    private ?float $Totalamount = null;

    #[ORM\ManyToOne(inversedBy: 'Expenses')]
    private ?Materials $materials = null;

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->dateE = new \DateTime(); // Set the current date 
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateE(): ?\DateTimeInterface
    {
        return $this->dateE;
    }

    public function setDateE(\DateTimeInterface $dateE): static
    {
        $this->dateE = $dateE;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): static
    {
        $this->Type = $Type;

        return $this;
    }

    public function getQuantityE(): ?float
    {
        return $this->QuantityE;
    }

    public function setQuantityE(float $QuantityE): static
    {
        $this->QuantityE = $QuantityE;

        return $this;
    }

    public function getCoast(): ?float
    {
        return $this->coast;
    }

    public function setCoast(float $coast): static
    {
        $this->coast = $coast;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->Totalamount;
    }

    public function setTotalAmount(float $Total_amount): static
    {
        $this->Totalamount = $Total_amount;

        return $this;
    }

    public function getMaterials(): ?Materials
    {
        return $this->materials;
    }

    public function setMaterials(?Materials $materials): static
    {
        $this->materials = $materials;

        return $this;
    }
}

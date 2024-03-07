<?php

namespace App\Entity;

use App\Repository\ExpensesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

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
    #[Assert\NotBlank(message: "Required ")]
    #[Assert\Positive(message: "Requires positive number")]
    private ?float $QuantityE = null;

    #[ORM\Column]
    #[Assert\Positive(message: "Requires positive number")]
    #[Assert\NotBlank(message: "Required , pelase fill it  ")]
    private ?float $coast = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Required more than 3 caracters ")]
    #[Assert\Length(min: 3)]
    private ?string $Description = null;

    #[ORM\Column]
    private ?float $Totalamount = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    private ?Products $product = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    private ?Supplier $supplier = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    private ?Capital $capital = null;

    #[ORM\Column]
    private ?bool $archived = null;

    #[ORM\OneToOne(targetEntity: Projects::class)]
    #[ORM\JoinColumn(name: "project_id", referencedColumnName: "id")]
    private ?Projects $project = null;

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

    public function setDateE(\DateTimeInterface $dateE): self
    {
        $this->dateE = $dateE;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    public function getQuantityE(): ?float
    {
        return $this->QuantityE;
    }

    public function setQuantityE(float $QuantityE): self
    {
        $this->QuantityE = $QuantityE;

        return $this;
    }

    public function getCoast(): ?float
    {
        return $this->coast;
    }

    public function setCoast(float $coast): self
    {
        $this->coast = $coast;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->Totalamount;
    }

    public function setTotalAmount(float $Total_amount): self
    {
        $this->Totalamount = $Total_amount;

        return $this;
    }

    public function getProduct(): ?Products
    {
        return $this->product;
    }

    public function setProduct(?Products $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getCapital(): ?Capital
    {
        return $this->capital;
    }

    public function setCapital(?Capital $capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    public function toggleArchived(): void
    {
        $this->archived = !$this->archived;
    }

    public function getProject(): ?Projects
    {
        return $this->project;
    }

    public function setProject(?Projects $project): self
    {
        $this->project = $project;

        return $this;
    }
}

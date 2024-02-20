<?php

namespace App\Entity;

use App\Repository\SellPurchaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Expr\Cast\Double;

#[ORM\Entity(repositoryClass: SellPurchaseRepository::class)]
class SellPurchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column(length: 255)]
    private ?string $Type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $TransactionDate = null;

    #[ORM\Column(length: 255)]
    private ?string $SupplierNameClientName = null;

    #[ORM\Column(length: 255)]
    private ?string $PorductService = null;

    #[ORM\Column]
    private ?int $Quantity = null;

    #[ORM\Column]
    private ?float $Coast = null;

    #[ORM\Column(length: 255)]
    private ?string $PaymentMethod = null;

    #[ORM\Column]
    private ?float $TotalAmount = null;

    #[ORM\Column(length: 255)]
    private ?string $Note = null;

    #[ORM\Column(nullable: true)]
    private ?float $Fund = null;




    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->Date = new \DateTime(); // Set the current date 
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): static
    {
        $this->Date = $Date;

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

    public function getTransactionDate(): ?\DateTimeInterface
    {
        return $this->TransactionDate;
    }

    public function setTransactionDate(\DateTimeInterface $TransactionDate): static
    {
        $this->TransactionDate = $TransactionDate;

        return $this;
    }

    public function getSupplierNameClientName(): ?string
    {
        return $this->SupplierNameClientName;
    }

    public function setSupplierNameClientName(string $SupplierNameClientName): static
    {
        $this->SupplierNameClientName = $SupplierNameClientName;

        return $this;
    }

    public function getPorductService(): ?string
    {
        return $this->PorductService;
    }

    public function setPorductService(string $PorductService): static
    {
        $this->PorductService = $PorductService;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->Quantity;
    }

    public function setQuantity(int $Quantity): static
    {
        $this->Quantity = $Quantity;

        return $this;
    }

    public function getCoast(): ?float
    {
        return $this->Coast;
    }

    public function setCoast(float $Coast): static
    {
        $this->Coast = $Coast;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->PaymentMethod;
    }

    public function setPaymentMethod(string $PaymentMethod): static
    {
        $this->PaymentMethod = $PaymentMethod;

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->TotalAmount;
    }

    public function setTotalAmount(float $TotalAmount): static
    {
        $this->TotalAmount = $TotalAmount;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->Note;
    }

    public function setNote(string $Note): static
    {
        $this->Note = $Note;

        return $this;
    }

    public function getFund(): ?float
    {
        return $this->Fund;
    }

    public function setFund(?float $Fund): static
    {
        $this->Fund = $Fund;

        return $this;
    }
}

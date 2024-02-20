<?php

namespace App\Entity;

use App\Repository\MaterialsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialsRepository::class)]
class Materials
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Type = null;

    #[ORM\Column(nullable: true)]
    private ?float $Quantity = null;

    #[ORM\Column]
    private ?float $UnitPrice = null;

    #[ORM\OneToMany(mappedBy: 'materials', targetEntity: Expenses::class)]
    private Collection $Expenses;

    public function __construct()
    {
        $this->Expenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuantity(): ?float
    {
        return $this->Quantity;
    }

    public function setQuantity(?float $Quantity): static
    {
        $this->Quantity = $Quantity;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->UnitPrice;
    }

    public function setUnitPrice(float $UnitPrice): static
    {
        $this->UnitPrice = $UnitPrice;

        return $this;
    }

    /**
     * @return Collection<int, Expenses>
     */
    public function getExpenses(): Collection
    {
        return $this->Expenses;
    }

    public function addExpense(Expenses $expense): static
    {
        if (!$this->Expenses->contains($expense)) {
            $this->Expenses->add($expense);
            $expense->setMaterials($this);
        }

        return $this;
    }

    public function removeExpense(Expenses $expense): static
    {
        if ($this->Expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getMaterials() === $this) {
                $expense->setMaterials(null);
            }
        }

        return $this;
    }
}

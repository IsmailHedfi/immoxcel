<?php

namespace App\Entity;

use App\Repository\CapitalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CapitalRepository::class)]
class Capital
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?string $Salary = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?string $Expensess = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?string $Funds = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?string $Profits = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?string $BigCapital = null;

    #[ORM\OneToMany(mappedBy: 'capital', targetEntity: Expenses::class)]
    private Collection $Transaction;

    public function __construct()
    {
        $this->Transaction = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalary(): ?string
    {
        return $this->Salary;
    }

    public function setSalary(string $Salary): static
    {
        $this->Salary = $Salary;

        return $this;
    }

    public function getExpensess(): ?string
    {
        return $this->Expensess;
    }

    public function setExpensess(string $Expensess): static
    {
        $this->Expensess = $Expensess;

        return $this;
    }

    public function getFunds(): ?string
    {
        return $this->Funds;
    }

    public function setFunds(string $Funds): static
    {
        $this->Funds = $Funds;

        return $this;
    }

    public function getProfits(): ?string
    {
        return $this->Profits;
    }

    public function setProfits(?string $Profits): static
    {
        $this->Profits = $Profits;

        return $this;
    }

    public function getBigCapital(): ?string
    {
        return $this->BigCapital;
    }

    public function setBigCapital(string $BigCapital): static
    {
        $this->BigCapital = $BigCapital;

        return $this;
    }

    /**
     * @return Collection<int, Expenses>
     */
    public function getTransaction(): Collection
    {
        return $this->Transaction;
    }

    public function addTransaction(Expenses $transaction): static
    {
        if (!$this->Transaction->contains($transaction)) {
            $this->Transaction->add($transaction);
            $transaction->setCapital($this);
        }

        return $this;
    }

    public function removeTransaction(Expenses $transaction): static
    {
        if ($this->Transaction->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCapital() === $this) {
                $transaction->setCapital(null);
            }
        }

        return $this;
    }
}

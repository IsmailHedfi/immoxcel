<?php

namespace App\Entity;

use App\Repository\EmployeesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeesRepository::class)]
class Employees
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $EmpName = null;

    #[ORM\Column(length: 255)]
    private ?string $EmpLastName = null;

    #[ORM\Column(length: 255)]
    private ?string $EmpSex = null;

    #[ORM\Column(length: 255)]
    private ?string $EmpEmail = null;

    #[ORM\Column(length: 255)]
    private ?string $EmpAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $EmpPhone = null;

    #[ORM\Column(length: 255)]
    private ?string $EmpFunction = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmpName(): ?string
    {
        return $this->EmpName;
    }

    public function setEmpName(string $EmpName): static
    {
        $this->EmpName = $EmpName;

        return $this;
    }

    public function getEmpLastName(): ?string
    {
        return $this->EmpLastName;
    }

    public function setEmpLastName(string $EmpLastName): static
    {
        $this->EmpLastName = $EmpLastName;

        return $this;
    }

    public function getEmpSex(): ?string
    {
        return $this->EmpSex;
    }

    public function setEmpSex(string $EmpSex): static
    {
        $this->EmpSex = $EmpSex;

        return $this;
    }

    public function getEmpEmail(): ?string
    {
        return $this->EmpEmail;
    }

    public function setEmpEmail(string $EmpEmail): static
    {
        $this->EmpEmail = $EmpEmail;

        return $this;
    }

    public function getEmpAddress(): ?string
    {
        return $this->EmpAddress;
    }

    public function setEmpAddress(string $EmpAddress): static
    {
        $this->EmpAddress = $EmpAddress;

        return $this;
    }

    public function getEmpPhone(): ?string
    {
        return $this->EmpPhone;
    }

    public function setEmpPhone(string $EmpPhone): static
    {
        $this->EmpPhone = $EmpPhone;

        return $this;
    }

    public function getEmpFunction(): ?string
    {
        return $this->EmpFunction;
    }

    public function setEmpFunction(string $EmpFunction): static
    {
        $this->EmpFunction = $EmpFunction;

        return $this;
    }
}

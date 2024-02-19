<?php

namespace App\Entity;

use App\Repository\EmployeesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeesRepository::class)]
class Employees
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Employee's name is required")]
    private ?string $EmpName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Employee's last name is required")]
    private ?string $EmpLastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Employee's sex is required")]
    private ?string $EmpSex = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"Employee's Email is required")]
    #[Assert\Email(message:"Employee's Email must be valid")]
    private ?string $EmpEmail = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Employee's address is required")]
    private ?string $EmpAddress = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Employee's phone number is required")]
    /*#[Assert\Regex(
        pattern: "/^\+(?:[0-9] ?){6,14}[0-9]$/",
        message: "Please enter a valid phone number."
    )]*/
    private ?string $EmpPhone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Employee's function is required")]
    private ?string $EmpFunction = null;

    #[ORM\OneToMany(mappedBy: 'Employee', targetEntity: Leaves::class, orphanRemoval: true)]
    private Collection $Leaves;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"Employee's birthday is required")]
    /*#[Assert\Expression(
        "value <= ('today') && value >= ('today -18 years')",
        message:"Employee must be at least 18 years old"
    )]*/
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"Employee's hire date is required")]
    private ?\DateTimeInterface $hireDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"Employee's end contract date is required")]
    #[Assert\GreaterThan(propertyPath: "hireDate")]
    private ?\DateTimeInterface $endContractDate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Employee's contract type is required")]
    private ?string $contractType = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Employee's allowed leave days is required")]
    private ?int $allowedLeaveDays = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $Empcv = null;

    public function __construct()
    {
        $this->Leaves = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Leaves>
     */
    public function getLeaves(): Collection
    {
        return $this->Leaves;
    }

    public function addLeaf(Leaves $leaf): static
    {
        if (!$this->Leaves->contains($leaf)) {
            $this->Leaves->add($leaf);
            $leaf->setEmployee($this);
        }

        return $this;
    }

    public function removeLeaf(Leaves $leaf): static
    {
        if ($this->Leaves->removeElement($leaf)) {
            // set the owning side to null (unless already changed)
            if ($leaf->getEmployee() === $this) {
                $leaf->setEmployee(null);
            }
        }

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getHireDate(): ?\DateTimeInterface
    {
        return $this->hireDate;
    }

    public function setHireDate(\DateTimeInterface $hireDate): static
    {
        $this->hireDate = $hireDate;

        return $this;
    }

    public function getEndContractDate(): ?\DateTimeInterface
    {
        return $this->endContractDate;
    }

    public function setEndContractDate(\DateTimeInterface $endContractDate): static
    {
        $this->endContractDate = $endContractDate;

        return $this;
    }

    public function getContractType(): ?string
    {
        return $this->contractType;
    }

    public function setContractType(string $contractType): static
    {
        $this->contractType = $contractType;

        return $this;
    }

    public function getAllowedLeaveDays(): ?int
    {
        return $this->allowedLeaveDays;
    }

    public function setAllowedLeaveDays(int $allowedLeaveDays): static
    {
        $this->allowedLeaveDays = $allowedLeaveDays;

        return $this;
    }

    public function getEmpcv()
    {
        return $this->Empcv;
    }

    public function setEmpcv($Empcv): static
    {
        $this->Empcv = $Empcv;

        return $this;
    }
}

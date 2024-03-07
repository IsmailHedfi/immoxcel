<?php

namespace App\Entity;

use App\Repository\LeavesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LeavesRepository::class)]
class Leaves
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Leave Type is required")]
    private ?string $LeaveType = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"Start date is required")]
    #[Assert\GreaterThanOrEqual('today')]
    private ?\DateTimeInterface $StartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"Finish date is required")]
     #[Assert\GreaterThanOrEqual(propertyPath: "StartDate")]
     private ?\DateTimeInterface $FinishDate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $Status = null;

    #[ORM\ManyToOne(inversedBy: 'Leaves')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Employees $Employee = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $LeaveDescription = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLeaveType(): ?string
    {
        return $this->LeaveType;
    }

    public function setLeaveType(string $LeaveType): static
    {
        $this->LeaveType = $LeaveType;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->StartDate;
    }

    public function setStartDate(\DateTimeInterface $StartDate): static
    {
        $this->StartDate = $StartDate;

        return $this;
    }

    public function getFinishDate(): ?\DateTimeInterface
    {
        return $this->FinishDate;
    }

    public function setFinishDate(\DateTimeInterface $FinishDate): static
    {
        $this->FinishDate = $FinishDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->Status;
    }

    public function setStatus(string $Status): static
    {
        $this->Status = $Status;

        return $this;
    }

    public function getEmployee(): ?Employees
    {
        return $this->Employee;
    }

    public function setEmployee(?Employees $Employee): static
    {
        $this->Employee = $Employee;

        return $this;
    }

    public function getLeaveDescription(): ?string
    {
        return $this->LeaveDescription;
    }

    public function setLeaveDescription(?string $LeaveDescription): static
    {
        $this->LeaveDescription = $LeaveDescription;

        return $this;
    }
    public function __construct()
    {
        $this->StartDate = new \DateTime();
        $this->FinishDate = new \DateTime();
    }
}

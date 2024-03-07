<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Project Name required")]
    private ?string $projectName = null;


    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: "Predicted start date must be a valid date")]
    private ?\DateTimeInterface $date_pred_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: "Predicted finish date must be a valid date")]
    #[Assert\GreaterThan(propertyPath: "date_pred_start", message: "Predicted finish date must be greater than the predicted start date")]
    private ?\DateTimeInterface $date_pred_finish = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: "Completion date must be a valid date")]
    private ?\DateTimeInterface $date_completion = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "float", message: "Budget must be a valid number")]
    #[Assert\PositiveOrZero(message: "Budget cannot be negative")]
    private ?float $budget = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "float", message: "Actual cost must be a valid number")]
    #[Assert\PositiveOrZero(message: "Actual cost cannot be negative")]
    private ?float $actual_cost = null;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'project', orphanRemoval: true)]
    private Collection $listTasks;

    #[ORM\ManyToMany(targetEntity: Employees::class, mappedBy: 'listProjects')]
    private Collection $listEmployees;

    public function __construct()
    {
        $this->listTasks = new ArrayCollection();
        $this->listEmployees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectName(): ?string
    {
        return $this->projectName;
    }

    public function setProjectName(string $projectName): static
    {
        $this->projectName = $projectName;

        return $this;
    }

    public function getDatePredStart(): ?\DateTimeInterface
    {
        return $this->date_pred_start;
    }

    public function setDatePredStart(?\DateTimeInterface $date_pred_start): static
    {
        $this->date_pred_start = $date_pred_start;

        return $this;
    }

    public function getDatePredFinish(): ?\DateTimeInterface
    {
        return $this->date_pred_finish;
    }

    public function setDatePredFinish(?\DateTimeInterface $date_pred_finish): static
    {
        $this->date_pred_finish = $date_pred_finish;

        return $this;
    }

    public function getDateCompletion(): ?\DateTimeInterface
    {
        return $this->date_completion;
    }

    public function setDateCompletion(?\DateTimeInterface $date_completion): static
    {
        $this->date_completion = $date_completion;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getActualCost(): ?float
    {
        return $this->actual_cost;
    }

    public function setActualCost(?float $actual_cost): static
    {
        $this->actual_cost = $actual_cost;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getListTasks(): Collection
    {
        return $this->listTasks;
    }

    public function addListTask(Task $listTask): static
    {
        if (!$this->listTasks->contains($listTask)) {
            $this->listTasks->add($listTask);
            $listTask->setProject($this);
        }

        return $this;
    }

    public function removeListTask(Task $listTask): static
    {
        if ($this->listTasks->removeElement($listTask)) {
            // set the owning side to null (unless already changed)
            if ($listTask->getProject() === $this) {
                $listTask->setProject(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->getProjectName();
    }

    /**
     * @return Collection<int, Employees>
     */
    public function getListEmployees(): Collection
    {
        return $this->listEmployees;
    }

    public function addListEmployee(Employees $listEmployee): static
    {
        if (!$this->listEmployees->contains($listEmployee)) {
            $this->listEmployees->add($listEmployee);
            $listEmployee->addListProject($this);
        }

        return $this;
    }

    public function removeListEmployee(Employees $listEmployee): static
    {
        if ($this->listEmployees->removeElement($listEmployee)) {
            $listEmployee->removeListProject($this);
        }

        return $this;
    }
}

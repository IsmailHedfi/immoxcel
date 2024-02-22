<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $projectName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_pred_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_pred_finish = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_completion = null;

    #[ORM\Column(nullable: true)]
    private ?float $budget = null;

    #[ORM\Column(nullable: true)]
    private ?float $actual_cost = null;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'project')]
    private Collection $listTasks;

    public function __construct()
    {
        $this->listTasks = new ArrayCollection();
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
}

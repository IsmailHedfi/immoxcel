<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $taskTitle = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $taskDescription = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $taskDeadline = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $taskStatus = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $taskCompletionDate = null;

    #[ORM\ManyToOne(inversedBy: 'listTasks')]
    private ?Project $project = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskTitle(): ?string
    {
        return $this->taskTitle;
    }

    public function setTaskTitle(string $taskTitle): static
    {
        $this->taskTitle = $taskTitle;

        return $this;
    }

    public function getTaskDescription(): ?string
    {
        return $this->taskDescription;
    }

    public function setTaskDescription(?string $taskDescription): static
    {
        $this->taskDescription = $taskDescription;

        return $this;
    }

    public function getTaskDeadline(): ?\DateTimeInterface
    {
        return $this->taskDeadline;
    }

    public function setTaskDeadline(?\DateTimeInterface $taskDeadline): static
    {
        $this->taskDeadline = $taskDeadline;

        return $this;
    }

    public function getTaskStatus(): ?string
    {
        return $this->taskStatus;
    }

    public function setTaskStatus(?string $taskStatus): static
    {
        $this->taskStatus = $taskStatus;

        return $this;
    }

    public function getTaskCompletionDate(): ?\DateTimeInterface
    {
        return $this->taskCompletionDate;
    }

    public function setTaskCompletionDate(?\DateTimeInterface $taskCompletionDate): static
    {
        $this->taskCompletionDate = $taskCompletionDate;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}

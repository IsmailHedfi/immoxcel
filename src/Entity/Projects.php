<?php

namespace App\Entity;

use App\Repository\ProjectsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ProjectsRepository::class)]
class Projects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Unique]
    private ?string $ProjectName = null;

    #[ORM\Column(length: 255)]
    private ?string $Descrption = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $PredStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $PredFinish = null;

    #[ORM\Column(length: 255)]
    private ?string $satuts = null;

    #[ORM\Column]
    private ?float $coast = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectName(): ?string
    {
        return $this->ProjectName;
    }

    public function setProjectName(string $ProjectName): static
    {
        $this->ProjectName = $ProjectName;

        return $this;
    }

    public function getDescrption(): ?string
    {
        return $this->Descrption;
    }

    public function setDescrption(string $Descrption): static
    {
        $this->Descrption = $Descrption;

        return $this;
    }

    public function getPredStart(): ?\DateTimeInterface
    {
        return $this->PredStart;
    }

    public function setPredStart(\DateTimeInterface $PredStart): static
    {
        $this->PredStart = $PredStart;

        return $this;
    }

    public function getPredFinish(): ?\DateTimeInterface
    {
        return $this->PredFinish;
    }

    public function setPredFinish(\DateTimeInterface $PredFinish): static
    {
        $this->PredFinish = $PredFinish;

        return $this;
    }

    public function getSatuts(): ?string
    {
        return $this->satuts;
    }

    public function setSatuts(string $satuts): static
    {
        $this->satuts = $satuts;

        return $this;
    }

    public function getCoast(): ?float
    {
        return $this->coast;
    }

    public function setCoast(float $coast): static
    {
        $this->coast = $coast;

        return $this;
    }
}

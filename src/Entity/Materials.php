<?php

namespace App\Entity;

use App\Repository\MaterialsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialsRepository::class)]
class Materials
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Type Material is required")]
    private ?string $TypeMaterial = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3)]
    #[Assert\NotBlank(message:"Unit Price is required")]
    #[Assert\Positive(message:"Unit Price should be positive")]
    private ?string $UnitPrice = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Quantity is required")]
    #[Assert\Positive(message:"Quantity should be positive")]
    private ?int $Quantity = null;

    #[ORM\ManyToOne(inversedBy: 'Materials')]
    private ?Depot $depot = null;

    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'listeMaterials')]
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeMaterial(): ?string
    {
        return $this->TypeMaterial;
    }

    public function setTypeMaterial(string $TypeMaterial): static
    {
        $this->TypeMaterial = $TypeMaterial;

        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->UnitPrice;
    }

    public function setUnitPrice(string $UnitPrice): static
    {
        $this->UnitPrice = $UnitPrice;

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

    public function getDepot(): ?Depot
    {
        return $this->depot;
    }

    public function setDepot(?Depot $depot): static
    {
        $this->depot = $depot;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addListeMaterial($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            $project->removeListeMaterial($this);
        }

        return $this;
    }
}

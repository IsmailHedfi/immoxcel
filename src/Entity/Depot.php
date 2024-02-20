<?php

namespace App\Entity;

use App\Repository\DepotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: DepotRepository::class)]
class Depot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Type Depot is required")]
    private ?string $location = null;

    #[ORM\OneToMany(targetEntity: Materials::class, mappedBy: 'depot')]
    private Collection $Materials;

    public function __construct()
    {
        $this->Materials = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, Materials>
     */
    public function getMaterials(): Collection
    {
        return $this->Materials;
    }

    public function addMaterial(Materials $material): static
    {
        if (!$this->Materials->contains($material)) {
            $this->Materials->add($material);
            $material->setDepot($this);
        }

        return $this;
    }

    public function removeMaterial(Materials $material): static
    {
        if ($this->Materials->removeElement($material)) {
            // set the owning side to null (unless already changed)
            if ($material->getDepot() === $this) {
                $material->setDepot(null);
            }
        }

        return $this;
    }
}

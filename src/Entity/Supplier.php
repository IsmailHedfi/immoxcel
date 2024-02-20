<?php

namespace App\Entity;

use App\Repository\SupplierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Mime\Message;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SupplierRepository::class)]
class Supplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Required ")]
    #[Assert\Length(min: 3)]
    private ?string $CompanyName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: " Required ")]
    #[Assert\Length(min: 3)]
    private ?string $Address = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Required")]
    private ?string $MaterialsS = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Assert\Positive(message: "Requires positive number")]
    private ?string $PhoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Patent refrence is required")]
    private ?string $PatentRef = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->CompanyName;
    }

    public function setCompanyName(string $CompanyName): static
    {
        $this->CompanyName = $CompanyName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->Address;
    }

    public function setAddress(string $Address): static
    {
        $this->Address = $Address;

        return $this;
    }

    public function getMaterialsS(): ?string
    {
        return $this->MaterialsS;
    }

    public function setMaterialsS(string $MaterialsS): static
    {
        $this->MaterialsS = $MaterialsS;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->PhoneNumber;
    }

    public function setPhoneNumber(string $PhoneNumber): static
    {
        $this->PhoneNumber = $PhoneNumber;

        return $this;
    }

    public function getPatentRef(): ?string
    {
        return $this->PatentRef;
    }

    public function setPatentRef(string $PatentRef): static
    {
        $this->PatentRef = $PatentRef;

        return $this;
    }
}

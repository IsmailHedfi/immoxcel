<?php

namespace App\Entity;
use App\Entity\Employees;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:5,minMessage:"Username must be at least 5 characters long")]
    #[Assert\NotBlank(message:"Username cannot be empty")]
    private ?string $Username = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:8,minMessage:"Password must be at least 8 characters long")]
    #[Assert\NotBlank(message:"Password cannot be empty")]
    private ?string $Password = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employees $Employee = null;

    
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->Username;
    }

    public function setUsername(string $Username): static
    {
        $this->Username = $Username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): static
    {
        $this->Password = $Password;

        return $this;
    }

    public function getEmployee(): ?Employees
    {
        return $this->Employee;
    }
    public function setEmployee(Employees $Employee): static
    {
        $this->Employee = $Employee;

        return $this;
    }

   

    
    public function getUserIdentifier(): ?string
    {
        return $this->Username;
    }

    public function getRoles(): array
    {
        // You can return an empty array if you don't have role-based access control
        return [];
    }

    public function getSalt(): ?string
    {
        // This method is deprecated since Symfony 5.3 and not needed anymore
        return null;
    }

    public function eraseCredentials()
    {
        // You can leave this method empty if you don't have sensitive data to erase
    }
}

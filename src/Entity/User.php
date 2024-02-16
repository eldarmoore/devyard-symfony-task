<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $loginTime = null;

    #[ORM\Column(length: 255)]
    private ?string $currency = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getLoginTime(): ?\DateTimeInterface
    {
        return $this->loginTime;
    }

    public function setLoginTime(?\DateTimeInterface $loginTime): static
    {
        $this->loginTime = $loginTime;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getRoles(): array
    {
        // Example: return a simple array of roles. Adjust based on your application's needs.
        return ['ROLE_USER'];
    }

    public function getSalt(): ?string
    {
        // If you are using bcrypt or sodium for password hashing, you don't need a salt.
        return null;
    }

    public function eraseCredentials()
    {
        // This is used to clean up any sensitive data you might have temporarily stored on the user object.
    }

    public function getUserIdentifier(): string
    {
        // This method is required in Symfony 5.3+ and replaces getUsername in new applications.
        // If you are implementing UserInterface from Symfony security core, and still use getUsername, ensure compatibility.
        return $this->username;
    }
}

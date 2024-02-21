<?php

namespace App\Entity;

use App\Repository\AgentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
class Agent implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $loginTime = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['Admin', 'Rep'])]
    private ?string $role = null;

    #[ORM\ManyToOne(targetEntity: Agent::class)]
    #[ORM\JoinColumn(name: "agent_id", referencedColumnName: "id", nullable: true)]
    private ?Agent $agentInCharge = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;  // Ensure to hash the password before setting it

        return $this;
    }

    public function getLoginTime(): ?\DateTimeInterface
    {
        return $this->loginTime;
    }

    public function setLoginTime(?\DateTimeInterface $loginTime): self
    {
        $this->loginTime = $loginTime;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getAgentInCharge(): ?Agent
    {
        return $this->agentInCharge;
    }

    public function setAgentInCharge(?Agent $agentInCharge): self
    {
        $this->agentInCharge = $agentInCharge;
        return $this;
    }

    public function getRoles(): array
    {
        // All agents get the ROLE_AGENT by default
        $roles = ['ROLE_AGENT'];

        // Add ROLE_ADMIN for agents with the admin type
        if ($this->getRole() === 'Admin') {
            $roles[] = 'ROLE_ADMIN';
        }

        return $roles;
    }

    public function getSalt(): null
    {
        return null;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}

<?php

namespace App\Entity;

use App\Repository\TradeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TradeRepository::class)]
class Trade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Agent::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Agent $agent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $tradeSize = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $lotCount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $pnl = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $payout = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $usedMargin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $entryRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $closeRate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateClose = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $position = null;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getTradeSize(): ?string
    {
        return $this->tradeSize;
    }

    public function setTradeSize(string $tradeSize): self
    {
        $this->tradeSize = $tradeSize;

        return $this;
    }

    public function getLotCount(): ?string
    {
        return $this->lotCount;
    }

    public function setLotCount(string $lotCount): static
    {
        $this->lotCount = $lotCount;

        return $this;
    }

    public function getPnl(): ?string
    {
        return $this->pnl;
    }

    public function setPnl(string $pnl): static
    {
        $this->pnl = $pnl;

        return $this;
    }

    public function getPayout(): ?string
    {
        return $this->payout;
    }

    public function setPayout(string $payout): static
    {
        $this->payout = $payout;

        return $this;
    }

    public function getUsedMargin(): ?string
    {
        return $this->usedMargin;
    }

    public function setUsedMargin(string $usedMargin): static
    {
        $this->usedMargin = $usedMargin;

        return $this;
    }

    public function getEntryRate(): ?string
    {
        return $this->entryRate;
    }

    public function setEntryRate(string $entryRate): static
    {
        $this->entryRate = $entryRate;

        return $this;
    }

    public function getCloseRate(): ?string
    {
        return $this->closeRate;
    }

    public function setCloseRate(?string $closeRate): static
    {
        $this->closeRate = $closeRate;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateClose(): ?\DateTimeInterface
    {
        return $this->dateClose;
    }

    public function setDateClose(?\DateTimeInterface $dateClose): static
    {
        $this->dateClose = $dateClose;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\AssetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssetRepository::class)]
class Asset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $bid = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $ask = null;

    private const LOT_SIZE = 10;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateUpdate = null;

    public function __construct()
    {
        $this->dateUpdate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBid(): ?string
    {
        return $this->bid;
    }

    public function setBid(string $bid): self
    {
        $this->bid = $bid;

        return $this;
    }

    public function getAsk(): ?string
    {
        return $this->ask;
    }

    public function setAsk(string $ask): self
    {
        $this->ask = $ask;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLotSize(): int
    {
        return self::LOT_SIZE;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(\DateTimeInterface $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }
}

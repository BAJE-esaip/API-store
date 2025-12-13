<?php

namespace App\Entity;

use App\Repository\DestructionItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DestructionItemRepository::class)]
class DestructionItem
{
    // #[ORM\Id]
    // #[ORM\GeneratedValue]
    // #[ORM\Column]
    // private ?int $id = null;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'destructionItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Destruction $destruction = null;

    #[ORM\Column]
    private ?float $quantity = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $reason = null;

    // public function getId(): ?int
    // {
    //     return $this->id;
    // }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getDestruction(): ?Destruction
    {
        return $this->destruction;
    }

    public function setDestruction(?Destruction $destruction): static
    {
        $this->destruction = $destruction;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }
}

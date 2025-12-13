<?php

namespace App\Entity;

use App\Repository\CorrectionItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CorrectionItemRepository::class)]
class CorrectionItem
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
    #[ORM\ManyToOne(inversedBy: 'correctionItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Correction $correction = null;

    #[ORM\Column]
    private ?float $newInventory = null;

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

    public function getCorrection(): ?Correction
    {
        return $this->correction;
    }

    public function setCorrection(?Correction $correction): static
    {
        $this->correction = $correction;

        return $this;
    }

    public function getNewInventory(): ?float
    {
        return $this->newInventory;
    }

    public function setNewInventory(float $newInventory): static
    {
        $this->newInventory = $newInventory;

        return $this;
    }
}

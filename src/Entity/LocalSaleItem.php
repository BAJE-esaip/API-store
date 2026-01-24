<?php

namespace App\Entity;

use App\Repository\LocalSaleItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: LocalSaleItemRepository::class)]
#[UniqueConstraint(name: 'UNIQUE_PRODUCT_LOCAL_SALE', fields: ['product', 'localSale'])]
class LocalSaleItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    // #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'localSaleItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LocalSale $localSale = null;

    #[ORM\Column]
    private ?float $quantity = null;

    #[ORM\Column]
    private ?float $unitPriceAtSale = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getLocalSale(): ?LocalSale
    {
        return $this->localSale;
    }

    public function setLocalSale(?LocalSale $localSale): static
    {
        $this->localSale = $localSale;

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

    public function getUnitPriceAtSale(): ?float
    {
        return $this->unitPriceAtSale;
    }

    public function setUnitPriceAtSale(float $unitPriceAtSale): static
    {
        $this->unitPriceAtSale = $unitPriceAtSale;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\MobileSaleItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: MobileSaleItemRepository::class)]
#[UniqueConstraint(name: 'UNIQUE_PRODUCT_MOBILE_SALE', fields: ['product', 'mobileSale'])]
class MobileSaleItem
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
    #[ORM\ManyToOne(inversedBy: 'mobileSaleItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MobileSale $mobileSale = null;

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

    public function getMobileSale(): ?MobileSale
    {
        return $this->mobileSale;
    }

    public function setMobileSale(?MobileSale $mobileSale): static
    {
        $this->mobileSale = $mobileSale;

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

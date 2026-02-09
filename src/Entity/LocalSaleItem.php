<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LocalSaleItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: LocalSaleItemRepository::class)]
#[UniqueConstraint(name: 'UNIQUE_PRODUCT_LOCAL_SALE', fields: ['product', 'localSale'])]
#[ApiResource(
    operations: [],
)]
class LocalSaleItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'local_sale:get',
        'local_sale:set',
    ])]
    private ?Product $product = null;

    // #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'localSaleItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LocalSale $localSale = null;

    #[ORM\Column]
    #[Groups([
        'local_sale:get',
        'local_sale:set',
    ])]
    private ?float $quantity = null;

    #[ORM\Column]
    #[Groups([
        'local_sale:get',
    ])]
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

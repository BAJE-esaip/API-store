<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
// use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    normalizationContext: [
        'groups' => ['product:get'],
        DateTimeNormalizer::FORMAT_KEY => \DateTimeInterface::RFC3339_EXTENDED,
    ],
    operations: [
        new GetCollection(
            // normalizationContext: [
            //     'groups' => ['product:getCollection'],
            //     // 'groups' => ['product:get'],
            // ],
            // security: "is_granted('ROLE_CHECKOUT')",
        ),
        new Get(
            // normalizationContext: [
            //     'groups' => ['product:get'],
            // ],
        ),
    ],
)]
class Product
{
    // use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups([
        'product:get',
    ])]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'product:get',
        'mobile_sale:get',
        'local_sale:get',
    ])]
    private ?string $label = null;

    #[ORM\Column]
    #[Groups([
        'product:get',
        'mobile_sale:get',
        'local_sale:get',
    ])]
    private ?float $unitPrice = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'product:get',
        'mobile_sale:get',
        'local_sale:get',
    ])]
    private ?float $unitWeight = null;

    #[ORM\Column]
    #[Groups([
        'product:get',
    ])]
    private ?float $inventory = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'product:get',
        'mobile_sale:get',
        'local_sale:get',
    ])]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'product:get',
    ])]
    private ?VatRate $vat = null;

    #[ORM\Column]
    #[Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getUnitWeight(): ?float
    {
        return $this->unitWeight;
    }

    public function setUnitWeight(?float $unitWeight): static
    {
        $this->unitWeight = $unitWeight;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getInventory(): ?float
    {
        return $this->inventory;
    }

    public function setInventory(float $inventory): static
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getVat(): ?VatRate
    {
        return $this->vat;
    }

    public function setVat(?VatRate $vat): static
    {
        $this->vat = $vat;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    #[Groups([
        'product:get',
    ])]
    public function getIsAvailable(): bool {
        return $this->deletedAt === null;
    }
}

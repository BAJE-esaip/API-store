<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\LocalSaleRepository;
use App\State\LocalSaleProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
// use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: LocalSaleRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['local_sale:get']],
    denormalizationContext: ['groups' => ['local_sale:set']],
    operations: [
        // CHECKOUT APP
        // create new sale
        new Post(
            security: 'is_granted("ROLE_CHECKOUT")',
            processor: LocalSaleProcessor::class,
        ),
        // CONTROL APP
        // get local sale with UUID
        new Get(
            security: 'is_granted("ROLE_CONTROL")',
        ),
    ],
)]
class LocalSale
{
    // use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    #[ORM\Column]
    #[Groups([
        'local_sale:get',
    ])]
    private ?float $total = null;

    #[ORM\ManyToOne(inversedBy: 'localSales')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    /**
     * @var Collection<int, LocalSaleItem>
     */
    #[ORM\OneToMany(
        targetEntity: LocalSaleItem::class,
        mappedBy: 'localSale',
        orphanRemoval: true,
        cascade: ['persist', 'remove'],
    )]
    #[Groups([
        'local_sale:get',
        'local_sale:set',
    ])]
    private Collection $localSaleItems;

    #[ORM\Column]
    #[Timestampable(on: 'create')]
    #[Groups([
        'local_sale:get',
    ])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Timestampable(on: 'update')]
    #[Groups([
        'local_sale:get',
    ])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->localSaleItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * @return Collection<int, LocalSaleItem>
     */
    public function getLocalSaleItems(): Collection
    {
        return $this->localSaleItems;
    }

    public function addLocalSaleItem(LocalSaleItem $localSaleItem): static
    {
        if (!$this->localSaleItems->contains($localSaleItem)) {
            $this->localSaleItems->add($localSaleItem);
            $localSaleItem->setLocalSale($this);
        }

        return $this;
    }

    public function removeLocalSaleItem(LocalSaleItem $localSaleItem): static
    {
        if ($this->localSaleItems->removeElement($localSaleItem)) {
            // set the owning side to null (unless already changed)
            if ($localSaleItem->getLocalSale() === $this) {
                $localSaleItem->setLocalSale(null);
            }
        }

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
}

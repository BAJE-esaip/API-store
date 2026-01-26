<?php

namespace App\Entity;

use App\Repository\MobileSaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Uid\Uuid;
// use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: MobileSaleRepository::class)]
class MobileSale
{
    // use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $uuid = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column]
    private ?bool $paid = null;

    /**
     * @var Collection<int, MobileSaleItem>
     */
    #[ORM\OneToMany(
        targetEntity: MobileSaleItem::class,
        mappedBy: 'mobileSale',
        orphanRemoval: true,
        cascade: ['persist', 'remove'],
    )]
    private Collection $mobileSaleItems;

    #[ORM\ManyToOne(inversedBy: 'mobileSales')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column]
    #[Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Timestampable()]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->mobileSaleItems = new ArrayCollection();
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

    public function isPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): static
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * @return Collection<int, MobileSaleItem>
     */
    public function getMobileSaleItems(): Collection
    {
        return $this->mobileSaleItems;
    }

    public function addMobileSaleItem(MobileSaleItem $mobileSaleItem): static
    {
        if (!$this->mobileSaleItems->contains($mobileSaleItem)) {
            $this->mobileSaleItems->add($mobileSaleItem);
            $mobileSaleItem->setMobileSale($this);
        }

        return $this;
    }

    public function removeMobileSaleItem(MobileSaleItem $mobileSaleItem): static
    {
        if ($this->mobileSaleItems->removeElement($mobileSaleItem)) {
            // set the owning side to null (unless already changed)
            if ($mobileSaleItem->getMobileSale() === $this) {
                $mobileSaleItem->setMobileSale(null);
            }
        }

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

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

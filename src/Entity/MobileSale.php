<?php

namespace App\Entity;

use App\Repository\MobileSaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MobileSaleRepository::class)]
class MobileSale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $clientId = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column]
    private ?bool $paid = null;

    /**
     * @var Collection<int, MobileSaleItem>
     */
    #[ORM\OneToMany(targetEntity: MobileSaleItem::class, mappedBy: 'mobileSale', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $mobileSaleItems;

    public function __construct()
    {
        $this->mobileSaleItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    public function setClientId(int $clientId): static
    {
        $this->clientId = $clientId;

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
}

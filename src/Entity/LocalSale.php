<?php

namespace App\Entity;

use App\Repository\LocalSaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocalSaleRepository::class)]
class LocalSale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\ManyToOne(inversedBy: 'localSales')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    /**
     * @var Collection<int, LocalSaleItem>
     */
    #[ORM\OneToMany(targetEntity: LocalSaleItem::class, mappedBy: 'localSale', orphanRemoval: true)]
    private Collection $LocalSaleItem;

    public function __construct()
    {
        $this->LocalSaleItem = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
    public function getLocalSaleItem(): Collection
    {
        return $this->LocalSaleItem;
    }

    public function addLocalSaleItem(LocalSaleItem $localSaleItem): static
    {
        if (!$this->LocalSaleItem->contains($localSaleItem)) {
            $this->LocalSaleItem->add($localSaleItem);
            $localSaleItem->setLocalSale($this);
        }

        return $this;
    }

    public function removeLocalSaleItem(LocalSaleItem $localSaleItem): static
    {
        if ($this->LocalSaleItem->removeElement($localSaleItem)) {
            // set the owning side to null (unless already changed)
            if ($localSaleItem->getLocalSale() === $this) {
                $localSaleItem->setLocalSale(null);
            }
        }

        return $this;
    }
}

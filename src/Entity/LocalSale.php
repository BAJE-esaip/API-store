<?php

namespace App\Entity;

use App\Repository\LocalSaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: LocalSaleRepository::class)]
class LocalSale
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $totalProfit = null;

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
    private Collection $localSaleItems;

    // #[ORM\Column]
    // private ?\DateTimeImmutable $createdAt = null;

    // #[ORM\Column]
    // private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->localSaleItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalProfit(): ?float
    {
        return $this->totalProfit;
    }

    public function setTotalProfit(float $totalProfit): static
    {
        $this->totalProfit = $totalProfit;

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

    // public function getCreatedAt(): ?\DateTimeImmutable
    // {
    //     return $this->createdAt;
    // }

    // public function setCreatedAt(\DateTimeImmutable $createdAt): static
    // {
    //     $this->createdAt = $createdAt;

    //     return $this;
    // }

    // public function getUpdatedAt(): ?\DateTimeImmutable
    // {
    //     return $this->updatedAt;
    // }

    // public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    // {
    //     $this->updatedAt = $updatedAt;

    //     return $this;
    // }
}

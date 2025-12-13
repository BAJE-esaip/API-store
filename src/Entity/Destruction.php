<?php

namespace App\Entity;

use App\Repository\DestructionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DestructionRepository::class)]
class Destruction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'destructions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    /**
     * @var Collection<int, DestructionItem>
     */
    #[ORM\OneToMany(targetEntity: DestructionItem::class, mappedBy: 'destruction', orphanRemoval: true)]
    private Collection $destructionItems;

    public function __construct()
    {
        $this->destructionItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, DestructionItem>
     */
    public function getDestructionItems(): Collection
    {
        return $this->destructionItems;
    }

    public function addDestructionItem(DestructionItem $destructionItem): static
    {
        if (!$this->destructionItems->contains($destructionItem)) {
            $this->destructionItems->add($destructionItem);
            $destructionItem->setDestruction($this);
        }

        return $this;
    }

    public function removeDestructionItem(DestructionItem $destructionItem): static
    {
        if ($this->destructionItems->removeElement($destructionItem)) {
            // set the owning side to null (unless already changed)
            if ($destructionItem->getDestruction() === $this) {
                $destructionItem->setDestruction(null);
            }
        }

        return $this;
    }
}

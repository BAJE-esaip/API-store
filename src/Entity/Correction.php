<?php

namespace App\Entity;

use App\Repository\CorrectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CorrectionRepository::class)]
class Correction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'corrections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    /**
     * @var Collection<int, CorrectionItem>
     */
    #[ORM\OneToMany(targetEntity: CorrectionItem::class, mappedBy: 'correction', orphanRemoval: true)]
    private Collection $correctionItems;

    public function __construct()
    {
        $this->correctionItems = new ArrayCollection();
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
     * @return Collection<int, CorrectionItem>
     */
    public function getCorrectionItems(): Collection
    {
        return $this->correctionItems;
    }

    public function addCorrectionItem(CorrectionItem $correctionItem): static
    {
        if (!$this->correctionItems->contains($correctionItem)) {
            $this->correctionItems->add($correctionItem);
            $correctionItem->setCorrection($this);
        }

        return $this;
    }

    public function removeCorrectionItem(CorrectionItem $correctionItem): static
    {
        if ($this->correctionItems->removeElement($correctionItem)) {
            // set the owning side to null (unless already changed)
            if ($correctionItem->getCorrection() === $this) {
                $correctionItem->setCorrection(null);
            }
        }

        return $this;
    }
}

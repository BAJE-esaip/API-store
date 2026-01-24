<?php

namespace App\Entity;

use App\Repository\CorrectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: CorrectionRepository::class)]
class Correction
{
    use TimestampableEntity;

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
    #[ORM\OneToMany(targetEntity: CorrectionItem::class, mappedBy: 'correction', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $correctionItems;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    // #[ORM\Column]
    // private ?\DateTimeImmutable $createdAt = null;

    // #[ORM\Column]
    // private ?\DateTimeImmutable $updatedAt = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // public function setCreatedAt(\DateTimeImmutable $createdAt): static
    // {
    //     $this->createdAt = $createdAt;

    //     return $this;
    // }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    // {
    //     $this->updatedAt = $updatedAt;

    //     return $this;
    // }
}

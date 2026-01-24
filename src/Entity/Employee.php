<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class Employee implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    /**
     * @var Collection<int, LocalSale>
     */
    #[ORM\OneToMany(targetEntity: LocalSale::class, mappedBy: 'employee')]
    private Collection $localSales;

    /**
     * @var Collection<int, Correction>
     */
    #[ORM\OneToMany(targetEntity: Correction::class, mappedBy: 'employee')]
    private Collection $corrections;

    // /**
    //  * @var Collection<int, Destruction>
    //  */
    // #[ORM\OneToMany(targetEntity: Destruction::class, mappedBy: 'employee')]
    // private Collection $destructions;

    /**
     * @var Collection<int, Purchase>
     */
    #[ORM\OneToMany(targetEntity: Purchase::class, mappedBy: 'employee')]
    private Collection $purchases;

    // #[ORM\Column]
    // #[Timestampable(on: 'create')]
    // private ?\DateTimeImmutable $createdAt = null;

    // #[ORM\Column]
    // #[Timestampable()]
    // private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct()
    {
        $this->localSales = new ArrayCollection();
        $this->corrections = new ArrayCollection();
        // $this->destructions = new ArrayCollection();
        $this->purchases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every employee at least has ROLE_USER and ROLE_EMPLOYEE
        // $roles[] = 'ROLE_USER';
        $roles[] = 'ROLE_EMPLOYEE';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, LocalSale>
     */
    public function getLocalSales(): Collection
    {
        return $this->localSales;
    }

    public function addLocalSale(LocalSale $localSale): static
    {
        if (!$this->localSales->contains($localSale)) {
            $this->localSales->add($localSale);
            $localSale->setEmployee($this);
        }

        return $this;
    }

    public function removeLocalSale(LocalSale $localSale): static
    {
        if ($this->localSales->removeElement($localSale)) {
            // set the owning side to null (unless already changed)
            if ($localSale->getEmployee() === $this) {
                $localSale->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Correction>
     */
    public function getCorrections(): Collection
    {
        return $this->corrections;
    }

    public function addCorrection(Correction $correction): static
    {
        if (!$this->corrections->contains($correction)) {
            $this->corrections->add($correction);
            $correction->setEmployee($this);
        }

        return $this;
    }

    public function removeCorrection(Correction $correction): static
    {
        if ($this->corrections->removeElement($correction)) {
            // set the owning side to null (unless already changed)
            if ($correction->getEmployee() === $this) {
                $correction->setEmployee(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, Destruction>
    //  */
    // public function getDestructions(): Collection
    // {
    //     return $this->destructions;
    // }

    // public function addDestruction(Destruction $destruction): static
    // {
    //     if (!$this->destructions->contains($destruction)) {
    //         $this->destructions->add($destruction);
    //         $destruction->setEmployee($this);
    //     }

    //     return $this;
    // }

    // public function removeDestruction(Destruction $destruction): static
    // {
    //     if ($this->destructions->removeElement($destruction)) {
    //         // set the owning side to null (unless already changed)
    //         if ($destruction->getEmployee() === $this) {
    //             $destruction->setEmployee(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): static
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setEmployee($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): static
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getEmployee() === $this) {
                $purchase->setEmployee(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        $fullName = trim(sprintf('%s %s', (string) $this->firstName, (string) $this->lastName));

        if ($fullName !== '') {
            return $fullName;
        }

        return (string) $this->username;
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

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}

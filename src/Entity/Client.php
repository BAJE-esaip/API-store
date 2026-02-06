<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\ClientRepository;
use App\State\AuthenticatedClientProvider;
use App\State\ClientRegistrationProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
// use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
// use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    normalizationContext: [
        'groups' => ['client:get'],
        DateTimeNormalizer::FORMAT_KEY => \DateTimeInterface::RFC3339_EXTENDED,
    ],
    denormalizationContext: [
        'groups' => ['client:set'],
    ],
    operations: [
        new Get(
            uriTemplate: '/clients/me',
            // security: "is_granted('ROLE_CLIENT')",
            // security: "is_granted('ROLE_ABC')",
            provider: AuthenticatedClientProvider::class,
        ),
        new Post(
            processor: ClientRegistrationProcessor::class,
        ),
        // new Patch(),
    ],
)]
#[UniqueEntity(fields: ['email'])]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    // use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups([
        'client:get',
    ])]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'client:get',
        'client:set',
    ])]
    #[Assert\Email()]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups([
        'client:get',
    ])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups([
        'client:set',
    ])]
    // #[Assert\NotBlank()]
    // TODO: add password validation
    #[Assert\Length(min: 8)]
    private ?string $password = null;

    /**
     * @var Collection<int, MobileSale>
     */
    #[ORM\OneToMany(targetEntity: MobileSale::class, mappedBy: 'client')]
    private Collection $mobileSales;

    #[ORM\Column]
    #[Timestampable(on: 'create')]
    #[Groups([
        'client:get',
    ])]
    // #[Context(normalizationContext: [
    //     DateTimeNormalizer::FORMAT_KEY => \DateTimeInterface::RFC3339_EXTENDED,
    // ])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Timestampable(on: 'update')]
    #[Groups([
        'client:get',
    ])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct()
    {
        $this->mobileSales = new ArrayCollection();
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function __toString(): string
    {
        return (string) ($this->email ?? $this->uuid ?? $this->id ?? '');
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every client at least has ROLE_USER and ROLE_CLIENT
        // $roles[] = 'ROLE_USER';
        $roles[] = 'ROLE_CLIENT';

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

    /**
     * @return Collection<int, MobileSale>
     */
    public function getMobileSales(): Collection
    {
        return $this->mobileSales;
    }

    public function addMobileSale(MobileSale $mobileSale): static
    {
        if (!$this->mobileSales->contains($mobileSale)) {
            $this->mobileSales->add($mobileSale);
            $mobileSale->setClient($this);
        }

        return $this;
    }

    public function removeMobileSale(MobileSale $mobileSale): static
    {
        if ($this->mobileSales->removeElement($mobileSale)) {
            // set the owning side to null (unless already changed)
            if ($mobileSale->getClient() === $this) {
                $mobileSale->setClient(null);
            }
        }

        return $this;
    }
}

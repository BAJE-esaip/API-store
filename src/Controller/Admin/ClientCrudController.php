<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Factory\RandomBasedUuidFactory;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use Symfony\Component\Form\FormBuilderInterface;

#[IsGranted('ROLE_ADMIN')]
class ClientCrudController extends AbstractCrudController
{
    private RandomBasedUuidFactory $randomUuidFactory;

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        UuidFactory $uuidFactory,
    ) {
        $this->randomUuidFactory = $uuidFactory->randomBased();
    }

    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('email'),
            TextField::new('password')
                ->setFormType(PasswordType::class)
                ->onlyWhenCreating(),
            TextField::new('newPassword', 'Nouveau mot de passe')
                ->setFormType(PasswordType::class)
                ->setFormTypeOptions([
                    'mapped' => false,
                    'required' => false,
                ])
                ->setHelp('Laissez vide pour garder le mot de passe actuel.')
                ->onlyWhenUpdating(),
            BooleanField::new('deleted', 'Compte supprimé')
                ->setFormTypeOptions(['mapped' => false, 'required' => false])
                ->onlyOnForms(),
            DateTimeField::new('createdAt')->onlyOnIndex(),
            DateTimeField::new('updatedAt')->onlyOnIndex(),
            DateTimeField::new('deletedAt')->onlyOnIndex(),
        ];
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $entity = $entityDto->getInstance();
        if ($entity instanceof Client && $entity->getUuid() === null) {
            $entity->setUuid($this->randomUuidFactory->create());
        }

        return parent::createNewFormBuilder($entityDto, $formOptions, $context);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Client) {
            $plainPassword = $entityInstance->getPassword();
            if ($plainPassword !== null && $plainPassword !== '') {
                $entityInstance->setPassword(
                    $this->passwordHasher->hashPassword($entityInstance, $plainPassword)
                );
            }

            $this->applyDeletedFlag($entityInstance);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Client) {
            $context = $this->getContext();
            $formName = $context?->getEntity()?->getName();
            $formData = $formName ? $context?->getRequest()?->request->all($formName) : [];
            $newPassword = is_array($formData) ? ($formData['newPassword'] ?? null) : null;

            if (is_string($newPassword) && $newPassword !== '') {
                $entityInstance->setPassword(
                    $this->passwordHasher->hashPassword($entityInstance, $newPassword)
                );
            }

            $this->applyDeletedFlag($entityInstance, $formData);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function applyDeletedFlag(Client $client, ?array $formData = null): void
    {
        $context = $this->getContext();
        $formName = $context?->getEntity()?->getName();
        $data = $formData ?? ($formName ? $context?->getRequest()?->request->all($formName) : []);
        $deleted = is_array($data) ? ($data['deleted'] ?? null) : null;

        if ($deleted === '1' || $deleted === 1 || $deleted === true) {
            if ($client->getDeletedAt() === null) {
                $client->setDeletedAt(new \DateTimeImmutable());
            }
        } else {
            $client->setDeletedAt(null);
        }
    }
}

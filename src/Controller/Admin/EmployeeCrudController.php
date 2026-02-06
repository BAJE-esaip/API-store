<?php

namespace App\Controller\Admin;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class EmployeeCrudController extends AbstractCrudController
{
    private array $roleChoices = [];

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private ParameterBagInterface $params,
    )
    {
        $this->roleChoices = $this->buildRoleChoices();
    }

    public static function getEntityFqcn(): string
    {
        return Employee::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('username'),
            TextField::new('firstName'),
            TextField::new('lastName'),
            TextField::new('email'),
            TextField::new('password')
                ->setFormType(PasswordType::class)
                ->onlyWhenCreating(),
            TextField::new('plainPassword', 'Nouveau mot de passe')
                ->setFormType(PasswordType::class)
                ->setFormTypeOptions([
                    'mapped' => false,
                    'required' => false,
                ])
                ->setHelp('Laissez vide pour garder le mot de passe actuel.')
                ->onlyWhenUpdating(),
            ChoiceField::new('roles')
                ->setChoices($this->roleChoices)
                ->allowMultipleChoices()
                ->formatValue(static function ($value): string {
                    if (!is_iterable($value)) {
                        return '';
                    }

                    $roles = [];
                    foreach ($value as $role) {
                        if ($role === 'ROLE_EMPLOYEE') {
                            continue;
                        }
                        $roles[] = (string) $role;
                    }

                    return implode(', ', $roles);
                }),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Employee) {
            $plainPassword = $entityInstance->getPassword();
            if ($plainPassword !== null && $plainPassword !== '') {
                $entityInstance->setPassword(
                    $this->passwordHasher->hashPassword($entityInstance, $plainPassword)
                );
            }

            $entityInstance->setRoles(array_values(array_unique($entityInstance->getRoles())));
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Employee) {
            $context = $this->getContext();
            $formName = $context?->getEntity()?->getName();
            $formData = $formName ? $context?->getRequest()?->request->all($formName) : [];
            $plainPassword = is_array($formData) ? ($formData['plainPassword'] ?? null) : null;

            if (is_string($plainPassword) && $plainPassword !== '') {
                $entityInstance->setPassword(
                    $this->passwordHasher->hashPassword($entityInstance, $plainPassword)
                );
            }

            $entityInstance->setRoles(array_values(array_unique($entityInstance->getRoles())));
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function buildRoleChoices(): array
    {
        $roles = ['ROLE_USER'];
        $hierarchy = $this->params->get('security.role_hierarchy.roles');

        if (is_array($hierarchy)) {
            foreach ($hierarchy as $role => $inherited) {
                $roles[] = $role;
                if (is_array($inherited)) {
                    foreach ($inherited as $childRole) {
                        $roles[] = $childRole;
                    }
                } elseif (is_string($inherited)) {
                    $roles[] = $inherited;
                }
            }
        }

        $roles = array_values(array_unique($roles));
        $choices = [];
        foreach ($roles as $role) {
            if ($role === 'ROLE_EMPLOYEE') {
                continue;
            }
            $choices[$role] = $role;
        }

        return $choices;
    }
}

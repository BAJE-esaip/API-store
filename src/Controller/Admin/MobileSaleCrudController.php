<?php

namespace App\Controller\Admin;

use App\Entity\MobileSale;
use App\Form\MobileSaleItemType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_CHECKOUT')]
class MobileSaleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MobileSale::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IntegerField::new('clientId'),
            NumberField::new('total'),
            BooleanField::new('paid'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::NEW, 'ROLE_CHECKOUT')
            ->setPermission(Action::EDIT, 'ROLE_CHECKOUT')
            ->setPermission(Action::DELETE, 'ROLE_CHECKOUT');
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        $this->addItemsForm($formBuilder);

        return $formBuilder;
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);
        $this->addItemsForm($formBuilder);

        return $formBuilder;
    }

    private function addItemsForm(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->add('mobileSaleItems', CollectionType::class, [
            'entry_type' => MobileSaleItemType::class,
            'by_reference' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'label' => 'Produits',
            'entry_options' => [
                'label' => false,
                'row_attr' => ['class' => 'js-collection-item'],
            ],
            'prototype' => true,
            'attr' => ['class' => 'js-collection'],
        ]);
    }
}

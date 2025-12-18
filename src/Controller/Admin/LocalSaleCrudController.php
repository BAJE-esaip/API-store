<?php

namespace App\Controller\Admin;

use App\Entity\LocalSale;
use App\Form\LocalSaleItemType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class LocalSaleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LocalSale::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('employee'),
            NumberField::new('total'),
        ];
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
        $formBuilder->add('localSaleItem', CollectionType::class, [
            'entry_type' => LocalSaleItemType::class,
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

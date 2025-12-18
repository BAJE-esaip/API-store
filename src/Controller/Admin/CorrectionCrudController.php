<?php

namespace App\Controller\Admin;

use App\Entity\Correction;
use App\Form\CorrectionItemType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class CorrectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Correction::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('employee'),
            TextField::new('correctionItemsSummary', 'Produits')
                ->setVirtual(true)
                ->formatValue(static function ($value, $entity): string {
                    if ($entity === null || !method_exists($entity, 'getCorrectionItems')) {
                        return '';
                    }

                    $labels = [];
                    foreach ($entity->getCorrectionItems() as $item) {
                        $product = $item->getProduct();
                        $productLabel = $product ? $product->getLabel() : 'Produit';
                        $labels[] = sprintf('%s -> %s', $productLabel, $item->getNewInventory());
                    }

                    return implode(', ', $labels);
                })
                ->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::NEW, 'ROLE_STOCK')
            ->setPermission(Action::EDIT, 'ROLE_STOCK')
            ->setPermission(Action::DELETE, 'ROLE_STOCK');
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
        $formBuilder->add('correctionItems', CollectionType::class, [
            'entry_type' => CorrectionItemType::class,
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

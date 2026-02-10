<?php

namespace App\Controller\Admin;

use App\Entity\Correction;
use App\Entity\CorrectionItem;
use App\Form\CorrectionItemType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class CorrectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Correction::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $correction = new Correction();
        $correction->addCorrectionItem(new CorrectionItem());

        return $correction;
    }

    public function configureFields(string $pageName): iterable
    {
        $readOnly = $pageName === Crud::PAGE_EDIT;

        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('employee')
                ->setFormTypeOption('disabled', $readOnly),
            TextField::new('description')
                ->setFormTypeOption('disabled', $readOnly)
                ->setRequired(true),
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
            ->setPermission(Action::DELETE, 'ROLE_STOCK')
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, static fn (Action $action) => $action->linkToCrudAction('edit'))
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN);
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
        $this->addItemsForm($formBuilder, true);

        return $formBuilder;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Correction) {
            $this->applyCorrectionToProducts($entityInstance, $entityManager);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Correction) {
            $this->applyCorrectionToProducts($entityInstance, $entityManager);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function addItemsForm(FormBuilderInterface $formBuilder, bool $readOnly = false): void
    {
        $formBuilder->add('correctionItems', CollectionType::class, [
            'entry_type' => CorrectionItemType::class,
            'by_reference' => false,
            'allow_add' => !$readOnly,
            'allow_delete' => !$readOnly,
            'label' => 'Produits',
            'entry_options' => [
                'label' => false,
                'disabled' => $readOnly,
                'row_attr' => ['class' => 'js-collection-item'],
            ],
            'prototype' => true,
            'disabled' => $readOnly,
            'attr' => [
                'class' => 'js-collection',
                'data-readonly' => $readOnly ? '1' : '0',
            ],
        ]);
    }

    private function applyCorrectionToProducts(Correction $correction, EntityManagerInterface $entityManager): void
    {
        foreach ($correction->getCorrectionItems() as $item) {
            $product = $item->getProduct();
            $newInventory = $item->getNewInventory();
            if ($product === null || $newInventory === null) {
                continue;
            }

            $product->setInventory($newInventory);
            $entityManager->persist($product);
        }
    }
}

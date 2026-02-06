<?php

namespace App\Controller\Admin;

use App\Entity\MobileSale;
use App\Form\MobileSaleItemType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
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
            AssociationField::new('client'),
            NumberField::new('total'),
            BooleanField::new('paid'),
            TextField::new('mobileSaleItemsSummary', 'Produits')
                ->setVirtual(true)
                ->formatValue(static function ($value, $entity): string {
                    if ($entity === null || !method_exists($entity, 'getMobileSaleItems')) {
                        return '';
                    }
                    $formatNumber = static fn (?float $number): string => $number === null
                        ? '-'
                        : number_format($number, 2, ',', ' ');

                    $rows = [];
                    foreach ($entity->getMobileSaleItems() as $item) {
                        $product = $item->getProduct();
                        $productLabel = $product ? $product->getLabel() : 'Produit';
                        $rows[] = sprintf(
                            '<li><strong>%s</strong> — Qté: %s — PU: %s</li>',
                            htmlspecialchars($productLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                            $formatNumber($item->getQuantity()),
                            $formatNumber($item->getUnitPriceAtSale())
                        );
                    }

                    if ($rows === []) {
                        return '<em>Aucun produit</em>';
                    }

                    return '<ul class="list-unstyled mb-0">'.implode('', $rows).'</ul>';
                })
                ->renderAsHtml()
                ->onlyOnDetail(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
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

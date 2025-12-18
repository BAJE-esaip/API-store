<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('label', 'Nom'),
            NumberField::new('unitPrice', 'Prix unitaire')->setNumDecimals(2),
            NumberField::new('unitWeight', 'Poids unitaire')->setNumDecimals(3)->setRequired(false),
            TextField::new('barcode', 'Code-barres')->setRequired(false),
            NumberField::new('inventory', 'Stock'),
            AssociationField::new('category', 'Catégorie'),
            AssociationField::new('vat', 'TVA'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::NEW, 'ROLE_STOCK')
            ->setPermission(Action::EDIT, 'ROLE_STOCK')
            ->setPermission(Action::DELETE, 'ROLE_STOCK');
    }
}

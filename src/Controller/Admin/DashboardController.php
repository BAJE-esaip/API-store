<?php

namespace App\Controller\Admin;

use App\Entity\Destruction;
use App\Repository\CategoryRepository;
use App\Repository\CorrectionRepository;
use App\Repository\DestructionRepository;
use App\Repository\EmployeeRepository;
use App\Repository\LocalSaleRepository;
use App\Repository\MobileSaleRepository;
use App\Repository\ProductRepository;
use App\Repository\PurchaseRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Employee;
use App\Entity\Client;
use App\Entity\Product;
use App\Entity\Correction;

use App\Entity\Category;
use App\Entity\VatRate;
use App\Entity\Purchase;

use App\Entity\MobileSale;

use App\Entity\LocalSale;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private ProductRepository $productRepository,
        private CategoryRepository $categoryRepository,
        private PurchaseRepository $purchaseRepository,
        private LocalSaleRepository $localSaleRepository,
        private MobileSaleRepository $mobileSaleRepository,
        private CorrectionRepository $correctionRepository,
        // private DestructionRepository $destructionRepository,
    ) {
    }

    public function index(): Response
    {
        $adminStats = null;
        $checkoutStats = null;
        $stockStats = null;

        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SYS_ADMIN')) {
            $adminStats = [
                'employees' => $this->employeeRepository->count([]),
                'categories' => $this->categoryRepository->count([]),
                'products' => $this->productRepository->count([]),
                'stockValue' => $this->calculateStockValue(),
                'purchasesCount' => $this->purchaseRepository->count([]),
                'purchasesTotal' => $this->sumField($this->purchaseRepository, 'p', 'total'),
            ];
        }

        if ($this->isGranted('ROLE_CHECKOUT') || $this->isGranted('ROLE_CHECKOUT_MANAGER')) {
            $checkoutStats = [
                'localSales' => [
                    'count' => $this->localSaleRepository->count([]),
                    'total' => $this->sumField($this->localSaleRepository, 'ls', 'total'),
                ],
                'mobileSales' => [
                    'count' => $this->mobileSaleRepository->count([]),
                    'total' => $this->sumField($this->mobileSaleRepository, 'ms', 'total'),
                    'unpaid' => (int) $this->mobileSaleRepository->createQueryBuilder('ms')
                        ->select('COUNT(ms.id)')
                        ->andWhere('ms.paid = :paid')
                        ->setParameter('paid', false)
                        ->getQuery()
                        ->getSingleScalarResult(),
                ],
            ];
        }

        if ($this->isGranted('ROLE_STOCK') || $this->isGranted('ROLE_CONTROL') || $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SYS_ADMIN')) {
            $stockStats = [
                'corrections' => $this->correctionRepository->count([]),
                // 'destructions' => $this->destructionRepository->count([]),
                'lowStock' => $this->getLowStockProducts(),
            ];
        }

        return $this->render('admin/dashboard.html.twig', [
            'user' => $this->getUser(),
            'adminStats' => $adminStats,
            'checkoutStats' => $checkoutStats,
            'stockStats' => $stockStats,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addJsFile('js/collection.js');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::subMenu('Administration', 'fas fa-cog')
            ->setPermission('ROLE_ADMIN')
            ->setSubItems([
                MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', Employee::class)->setPermission('ROLE_ADMIN'),
                MenuItem::linkToCrud('Clients', 'fas fa-users', Client::class)->setPermission('ROLE_ADMIN'),
                MenuItem::linkToCrud('TVA', 'fas fa-ellipsis-v', VatRate::class)->setPermission('ROLE_ADMIN'),
            ]);

        yield MenuItem::subMenu('Comptabilité', 'fas fa-book')
            ->setPermission('ROLE_ADMIN')
            ->setSubItems([
                MenuItem::linkToCrud('Historique achats', 'fas fa-credit-card', Purchase::class)->setPermission('ROLE_ADMIN'),
                MenuItem::linkToCrud('Vente Local', 'fas fa-credit-card', LocalSale::class)->setPermission('ROLE_ADMIN'),
                MenuItem::linkToCrud('Vente Mobile', 'fas fa-credit-card', MobileSale::class)->setPermission('ROLE_ADMIN'),
            ]);

        yield MenuItem::subMenu('Inventaire', 'fas fa-list')->setSubItems([
            MenuItem::linkToCrud('Produits', 'fas fa-inbox', Product::class),
            MenuItem::linkToCrud('Catégories', 'fas fa-folder', Category::class),
            MenuItem::linkToCrud('Correction', 'fas fa-eraser', Correction::class),
            // MenuItem::linkToCrud('Destruction', 'fas fa-trash', Destruction::class),
        ]);
    }

    private function sumField(ServiceEntityRepository $repository, string $alias, string $field): float
    {
        $queryBuilder = $repository->createQueryBuilder($alias)
            ->select(sprintf('COALESCE(SUM(%s.%s), 0)', $alias, $field));

        return (float) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    private function calculateStockValue(): float
    {
        $queryBuilder = $this->productRepository->createQueryBuilder('p')
            ->select('COALESCE(SUM(p.inventory * p.unitPrice), 0)');

        return (float) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return list<Product>
     */
    private function getLowStockProducts(): array
    {
        return $this->productRepository->createQueryBuilder('p')
            ->orderBy('p.inventory', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}

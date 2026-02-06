<?php

namespace App\Command;

use App\Entity\Client;
use App\Entity\Correction;
use App\Entity\CorrectionItem;
use App\Entity\Employee;
use App\Entity\LocalSale;
use App\Entity\LocalSaleItem;
use App\Entity\MobileSale;
use App\Entity\MobileSaleItem;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\VatRate;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
// use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
// use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Factory\RandomBasedUuidFactory;
use Symfony\Component\Uid\Factory\UuidFactory;

#[AsCommand(
    name: 'app:seed',
    description: 'Seed the database with test data',
)]
class SeedTestDataCommand extends Command {
    private RandomBasedUuidFactory $randomUuidFactory;

    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        UuidFactory $uuidFactory,
    ) {
        parent::__construct();
        $this->randomUuidFactory = $uuidFactory->randomBased();
    }

    // protected function configure(): void {
    //     $this
    //         ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
    //         ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
    //     ;
    // }

    // protected function execute(InputInterface $input, OutputInterface $output): int {
    //     $io = new SymfonyStyle($input, $output);
    //     $arg1 = $input->getArgument('arg1');

    //     if ($arg1) {
    //         $io->note(sprintf('You passed an argument: %s', $arg1));
    //     }

    //     if ($input->getOption('option1')) {
    //         // ...
    //     }

    //     $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

    //     return Command::SUCCESS;
    // }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $entityCounter = 0;

        /////////////////
        /// EMPLOYEES ///
        /////////////////

        /**
         * @var Employee[]
         */
        $employees = [];
        $employeesCreationDateTime = new DateTimeImmutable('2026-01-01 12:00:00');

        $employees[0] = (new Employee())
            ->setEmail('alice.dubois@internal.local')
            ->setUsername('alice_dubois')
            ->setFirstName('Alice')
            ->setLastName('Dubois')
            ->setCreatedAt($employeesCreationDateTime)
            ->setUpdatedAt($employeesCreationDateTime)
            ;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $employees[0],
            '1234azerty',
        );
        $employees[0]->setPassword($hashedPassword);

        $employees[1] = (new Employee())
            ->setEmail('thomas.lefebvre@internal.local')
            ->setUsername('thomas_lefebvre')
            ->setFirstName('Thomas')
            ->setLastName('Lefebvre')
            ->setRoles(['ROLE_STOCK'])
            ->setCreatedAt($employeesCreationDateTime)
            ->setUpdatedAt($employeesCreationDateTime)
            ;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $employees[1],
            '4321azerty',
        );
        $employees[1]->setPassword($hashedPassword);

        $employees[2] = (new Employee())
            ->setEmail('lea.moreau@internal.local')
            ->setUsername('lea_moreau')
            ->setFirstName('Léa')
            ->setLastName('Moreau')
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedAt($employeesCreationDateTime)
            ->setUpdatedAt($employeesCreationDateTime)
            ;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $employees[2],
            'paris12345',
        );
        $employees[2]->setPassword($hashedPassword);

        $employees[3] = (new Employee())
            ->setEmail('nicolas.betrand@internal.local')
            ->setUsername('nicolas_betrand')
            ->setFirstName('Nicolas')
            ->setLastName('Betrand')
            ->setRoles(['ROLE_CONTROL'])
            ->setCreatedAt($employeesCreationDateTime)
            ->setUpdatedAt($employeesCreationDateTime)
            ;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $employees[3],
            '9999abcd',
        );
        $employees[3]->setPassword($hashedPassword);

        $employees[4] = (new Employee())
            ->setEmail('camille.rousseau@internal.local')
            ->setUsername('camille_rousseau')
            ->setFirstName('Camille')
            ->setLastName('Rousseau')
            ->setRoles(['ROLE_CHECKOUT'])
            ->setCreatedAt($employeesCreationDateTime)
            ->setUpdatedAt($employeesCreationDateTime)
            ;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $employees[4],
            '123456789',
        );
        $employees[4]->setPassword($hashedPassword);

        $employees[5] = (new Employee())
            ->setEmail('julien.girard@internal.local')
            ->setUsername('julien_girard')
            ->setFirstName('Julien')
            ->setLastName('Girard')
            ->setRoles(['ROLE_CHECKOUT_MANAGER'])
            ->setCreatedAt($employeesCreationDateTime)
            ->setUpdatedAt($employeesCreationDateTime)
            ;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $employees[5],
            '0000abcd',
        );
        $employees[5]->setPassword($hashedPassword);

        $employees[6] = (new Employee())
            ->setEmail('manon.petit@internal.local')
            ->setUsername('manon_petit')
            ->setFirstName('Manon')
            ->setLastName('Petit')
            ->setRoles(['ROLE_SYS_ADMIN'])
            ->setCreatedAt($employeesCreationDateTime)
            ->setUpdatedAt($employeesCreationDateTime)
            ;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $employees[6],
            'qsdfmlkj',
        );
        $employees[6]->setPassword($hashedPassword);

        $io->text('Persisting employees ...');
        foreach ($employees as $employee) {
            $this->em->persist($employee);
            $entityCounter++;
        }

        ///////////////
        /// CLIENTS ///
        ///////////////

        /**
         * @var Client[]
         */
        $clients = [];

        $client0CreationDatetime = new DateTimeImmutable('2026-01-05 10:52:27');
        $clients[0] = (new Client())
            ->setUuid($this->randomUuidFactory->create())
            ->setEmail('hugo.marchand@mail.local')
            ->setCreatedAt($client0CreationDatetime)
            ->setUpdatedAt($client0CreationDatetime)
            ;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $clients[0],
            'azerty1234',
        );
        $clients[0]->setPassword($hashedPassword);

        $client1CreationDatetime = new DateTimeImmutable('2026-01-03 17:40:41');
        $clients[1] = (new Client())
            ->setUuid($this->randomUuidFactory->create())
            ->setEmail('chloe.dubois@mail.local')
            ->setCreatedAt($client1CreationDatetime)
            ->setUpdatedAt($client1CreationDatetime)
            ;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $clients[1],
            'azerty1234',
        );
        $clients[1]->setPassword($hashedPassword);

        $client2CreationDatetime = new DateTimeImmutable('2026-01-06 13:03:13');
        $clients[2] = (new Client())
            ->setUuid($this->randomUuidFactory->create())
            ->setEmail('lucas.fontaine@mail.local')
            ->setCreatedAt($client2CreationDatetime)
            ->setUpdatedAt($client2CreationDatetime)
            ;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $clients[2],
            '4321reza',
        );
        $clients[2]->setPassword($hashedPassword);

        $io->text('Persisting clients ...');
        foreach ($clients as $client) {
            $this->em->persist($client);
            $entityCounter++;
        }

        /////////////////
        /// VAT RATES ///
        /////////////////

        /**
         * @var VatRate[]
         */
        $vatRates = [];

        $vatRates[0] = (new VatRate())->setRate(0.055);
        $vatRates[1] = (new VatRate())->setRate(0.1);
        $vatRates[2] = (new VatRate())->setRate(0.2);

        $io->text('Persisting vat rates ...');
        foreach ($vatRates as $vatRate) {
            $this->em->persist($vatRate);
            $entityCounter++;
        }

        //////////////////
        /// CATEGORIES ///
        //////////////////

        /**
         * @var Category[]
         */
        $categories = [];

        $categories[0] = (new Category())
            ->setLabel('Fruits et légumes')
            ->setWeighable(true)
            ;
        $categories[1] = (new Category())
            ->setLabel('Boulangerie')
            ->setWeighable(false)
            ;
        $categories[2] = (new Category())
            ->setLabel('Épicerie Salée')
            ->setWeighable(false)
            ;
        $categories[3] = (new Category())
            ->setLabel('Traiteur')
            ->setWeighable(true)
            ;
        $categories[4] = (new Category())
            ->setLabel('Boissons')
            ->setWeighable(false)
            ;
        $categories[5] = (new Category())
            ->setLabel('Électroménager')
            ->setWeighable(false)
            ;

        $io->text('Persisting categories ...');
        foreach ($categories as $category) {
            $this->em->persist($category);
            $entityCounter++;
        }

        ////////////////
        /// PRODUCTS ///
        ////////////////

        /**
         * @var Product[]
         */
        $products = [];
        $productsCreationDateTime = new DateTimeImmutable('2026-01-01 12:00:00');

        $products[0] = (new Product())
            ->setLabel('Pommes Gala')
            ->setCategory($categories[0])
            ->setUnitPrice(2.5)
            ->setUnitWeight(1)
            ->setInventory(45.5)
            ->setVat($vatRates[0])
            ->setCode('04133')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[1] = (new Product())
            ->setLabel('Carottes Vrac')
            ->setCategory($categories[0])
            ->setUnitPrice(1.2)
            ->setUnitWeight(1)
            ->setInventory(120)
            ->setVat($vatRates[0])
            ->setCode('04562')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[2] = (new Product())
            ->setLabel('Bananes Bio')
            ->setCategory($categories[0])
            ->setUnitPrice(1.99)
            ->setUnitWeight(1)
            ->setInventory(30.2)
            ->setVat($vatRates[0])
            ->setCode('04011')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[3] = (new Product())
            ->setLabel('Baguette Tradition')
            ->setCategory($categories[1])
            ->setUnitPrice(0.95)
            ->setUnitWeight(0.25)
            ->setInventory(80)
            ->setVat($vatRates[0])
            ->setCode('3456789012340')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[4] = (new Product())
            ->setLabel('Pain de Mie complet')
            ->setCategory($categories[1])
            ->setUnitPrice(2.10)
            ->setUnitWeight(0.50)
            ->setInventory(25)
            ->setVat($vatRates[0])
            ->setCode('3123456789019')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[5] = (new Product())
            ->setLabel('Croissant Pur Beurre')
            ->setCategory($categories[1])
            ->setUnitPrice(1.10)
            ->setUnitWeight(0.06)
            ->setInventory(40)
            ->setVat($vatRates[0])
            ->setCode('3234567890126')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[6] = (new Product())
            ->setLabel('Pâtes Fusilli 500g')
            ->setCategory($categories[2])
            ->setUnitPrice(1.45)
            ->setUnitWeight(0.50)
            ->setInventory(150)
            ->setVat($vatRates[0])
            ->setCode('3012345678902')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[7] = (new Product())
            ->setLabel('Riz Basmati 1kg')
            ->setCategory($categories[2])
            ->setUnitPrice(2.80)
            ->setUnitWeight(1.00)
            ->setInventory(90)
            ->setVat($vatRates[0])
            ->setCode('3023456789012')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[8] = (new Product())
            ->setLabel('Chips de Crevettes')
            ->setCategory($categories[2])
            ->setUnitPrice(1.85)
            ->setUnitWeight(0.10)
            ->setInventory(45)
            ->setVat($vatRates[0])
            ->setCode('3034567890122')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[9] = (new Product())
            ->setLabel('Sel de Guérande')
            ->setCategory($categories[2])
            ->setUnitPrice(3.20)
            ->setUnitWeight(0.25)
            ->setInventory(30)
            ->setVat($vatRates[0])
            ->setCode('3045678901232')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[10] = (new Product())
            ->setLabel('Comté AOP (Vrac)')
            ->setCategory($categories[3])
            ->setUnitPrice(18.50)
            ->setUnitWeight(1)
            ->setInventory(12.4)
            ->setVat($vatRates[0])
            ->setCode('08201')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[11] = (new Product())
            ->setLabel('Jambon Blanc (Vrac)')
            ->setCategory($categories[3])
            ->setUnitPrice(22.00)
            ->setUnitWeight(1)
            ->setInventory(5.8)
            ->setVat($vatRates[0])
            ->setCode('08502')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[12] = (new Product())
            ->setLabel('Olives Vertes')
            ->setCategory($categories[3])
            ->setUnitPrice(14.00)
            ->setUnitWeight(1)
            ->setInventory(8.5)
            ->setVat($vatRates[0])
            ->setCode('08990')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[13] = (new Product())
            ->setLabel('Eau de Source 1.5L')
            ->setCategory($categories[4])
            ->setUnitPrice(0.45)
            ->setUnitWeight(1.50)
            ->setInventory(300)
            ->setVat($vatRates[0])
            ->setCode('3512345678907')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[14] = (new Product())
            ->setLabel('Soda Cola 33cl')
            ->setCategory($categories[4])
            ->setUnitPrice(0.85)
            ->setUnitWeight(0.33)
            ->setInventory(120)
            ->setVat($vatRates[0])
            ->setCode('3523456789017')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[15] = (new Product())
            ->setLabel('Jus d\'Orange Frais')
            ->setCategory($categories[4])
            ->setUnitPrice(3.50)
            ->setUnitWeight(1.00)
            ->setInventory(15)
            ->setVat($vatRates[0])
            ->setCode('3534567890127')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[16] = (new Product())
            ->setLabel('Vin Rouge Bordeaux')
            ->setCategory($categories[4])
            ->setUnitPrice(8.90)
            ->setUnitWeight(0.75)
            ->setInventory(48)
            ->setVat($vatRates[2])
            ->setCode('3545678901237')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[17] = (new Product())
            ->setLabel('Huile d\'Olive 75cl')
            ->setCategory($categories[2])
            ->setUnitPrice(7.40)
            ->setUnitWeight(0.75)
            ->setInventory(40)
            ->setVat($vatRates[0])
            ->setCode('3056789012342')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[18] = (new Product())
            ->setLabel('Brioche Tressée')
            ->setCategory($categories[1])
            ->setUnitPrice(3.90)
            ->setUnitWeight(0.40)
            ->setInventory(12)
            ->setVat($vatRates[0])
            ->setCode('3245678901236')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[19] = (new Product())
            ->setLabel('Machine à laver')
            ->setCategory($categories[5])
            ->setUnitPrice(109.99)
            ->setUnitWeight(10.5)
            ->setInventory(25)
            ->setVat($vatRates[2])
            ->setCode('3268478537894')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;
        $products[20] = (new Product())
            ->setLabel('Four à micro-ondes')
            ->setCategory($categories[5])
            ->setUnitPrice(59.99)
            ->setUnitWeight(6.25)
            ->setInventory(33)
            ->setVat($vatRates[2])
            ->setCode('3114633544279')
            ->setCreatedAt($productsCreationDateTime)
            ->setUpdatedAt($productsCreationDateTime)
            ;

        $io->text('Persisting products ...');
        foreach ($products as $product) {
            $this->em->persist($product);
            $entityCounter++;
        }

        ///////////////////
        /// LOCAL SALES ///
        ///////////////////

        /**
         * @var LocalSale[]
         */
        $localSales = [];

        $localSale0CreationDate = new DateTimeImmutable('2026-01-03 13:24:04');
        $localSales[0] = (new LocalSale())
            ->setUuid($this->randomUuidFactory->create())
            ->setTotal(6.6)
            ->setCreatedAt($localSale0CreationDate)
            ->setUpdatedAt($localSale0CreationDate)
            ->setEmployee($employees[4])
            ;
        $localSale1CreationDate = new DateTimeImmutable('2026-01-07 09:12:01');
        $localSales[1] = (new LocalSale())
            ->setUuid($this->randomUuidFactory->create())
            ->setTotal(9.05)
            ->setCreatedAt($localSale1CreationDate)
            ->setUpdatedAt($localSale1CreationDate)
            ->setEmployee($employees[4])
            ;
        $localSale2CreationDate = new DateTimeImmutable('2026-01-14 15:45:42');
        $localSales[2] = (new LocalSale())
            ->setUuid($this->randomUuidFactory->create())
            ->setTotal(113.29)
            ->setCreatedAt($localSale2CreationDate)
            ->setUpdatedAt($localSale2CreationDate)
            ->setEmployee($employees[4])
            ;

        $io->text('Persisting local sales ...');
        foreach ($localSales as $localSale) {
            $this->em->persist($localSale);
            $entityCounter++;
        }

        ////////////////////////
        /// LOCAL SALE ITEMS ///
        ////////////////////////

        /**
         * @var LocalSaleItem[]
         */
        $localSaleItems = [];

        $localSaleItems[0] = (new LocalSaleItem())
            ->setLocalSale($localSales[0])
            ->setProduct($products[7])
            ->setQuantity(2)
            ->setUnitPriceAtSale(2.80)
            ;
        $localSaleItems[1] = (new LocalSaleItem())
            ->setLocalSale($localSales[0])
            ->setProduct($products[2])
            ->setQuantity(.5)
            ->setUnitPriceAtSale(1.99)
            ;
        $localSaleItems[2] = (new LocalSaleItem())
            ->setLocalSale($localSales[1])
            ->setProduct($products[5])
            ->setQuantity(5)
            ->setUnitPriceAtSale(1.10)
            ;
        $localSaleItems[3] = (new LocalSaleItem())
            ->setLocalSale($localSales[1])
            ->setProduct($products[14])
            ->setQuantity(2)
            ->setUnitPriceAtSale(.85)
            ;
        $localSaleItems[4] = (new LocalSaleItem())
            ->setLocalSale($localSales[1])
            ->setProduct($products[8])
            ->setQuantity(1)
            ->setUnitPriceAtSale(1.85)
            ;
        $localSaleItems[5] = (new LocalSaleItem())
            ->setLocalSale($localSales[2])
            ->setProduct($products[11])
            ->setQuantity(.15)
            ->setUnitPriceAtSale(22.00)
            ;
        $localSaleItems[6] = (new LocalSaleItem())
            ->setLocalSale($localSales[2])
            ->setProduct($products[19])
            ->setQuantity(1)
            ->setUnitPriceAtSale(109.99)
            ;

        $io->text('Persisting local sale items ...');
        foreach ($localSaleItems as $localSaleItem) {
            $this->em->persist($localSaleItem);
            $entityCounter++;
        }

        ///////////////////
        /// CORRECTIONS ///
        ///////////////////

        /**
         * @var Correction[]
         */
        $corrections = [];

        $correction0CreationDate = new DateTimeImmutable('2026-01-03 17:32:15');
        $corrections[0] = (new Correction())
            ->setEmployee($employees[1])
            ->setDescription('Inventaire électroménager')
            ->setCreatedAt($correction0CreationDate)
            ->setUpdatedAt($correction0CreationDate)
            ;
        $correction1CreationDate = new DateTimeImmutable('2026-01-12 11:01:55');
        $corrections[1] = (new Correction())
            ->setEmployee($employees[1])
            ->setDescription('Destruction de produits périmés')
            ->setCreatedAt($correction1CreationDate)
            ->setUpdatedAt($correction1CreationDate)
            ;

        $io->text('Persisting corrections ...');
        foreach ($corrections as $correction) {
            $this->em->persist($correction);
            $entityCounter++;
        }

        ////////////////////////
        /// CORRECTION ITEMS ///
        ////////////////////////

        /**
         * @var CorrectionItem[]
         */
        $correctionItems = [];

        $correctionItems[0] = (new CorrectionItem())
            ->setCorrection($corrections[0])
            ->setProduct($products[19])
            ->setNewInventory(26)
            ;
        $correctionItems[1] = (new CorrectionItem())
            ->setCorrection($corrections[0])
            ->setProduct($products[20])
            ->setNewInventory(33)
            ;
        $correctionItems[2] = (new CorrectionItem())
            ->setCorrection($corrections[1])
            ->setProduct($products[0])
            ->setNewInventory(45.5)
            ;
        $correctionItems[3] = (new CorrectionItem())
            ->setCorrection($corrections[1])
            ->setProduct($products[5])
            ->setNewInventory(40)
            ;
        $correctionItems[4] = (new CorrectionItem())
            ->setCorrection($corrections[1])
            ->setProduct($products[15])
            ->setNewInventory(15)
            ;

        $io->text('Persisting correction items ...');
        foreach ($correctionItems as $correctionItem) {
            $this->em->persist($correctionItem);
            $entityCounter++;
        }

        /////////////////
        /// PURCHASES ///
        /////////////////

        /**
         * @var Purchase[]
         */
        $purchases = [];

        $purchase0CreationDate = new DateTimeImmutable('2026-01-20 13:15:10');
        $purchases[0] = (new Purchase())
            ->setEmployee($employees[1])
            ->setTotal(112.95)
            ->setCreatedAt($purchase0CreationDate)
            ->setUpdatedAt($purchase0CreationDate)
            ;

        $io->text('Persisting purchases ...');
        foreach ($purchases as $purchase) {
            $this->em->persist($purchase);
            $entityCounter++;
        }

        //////////////////////
        /// PURCHASE ITEMS ///
        //////////////////////

        /**
         * @var PurchaseItem[]
         */
        $purchaseItems = [];

        $purchaseItems[0] = (new PurchaseItem())
            ->setPurchase($purchases[0])
            ->setProduct($products[16])
            ->setQuantity(12)
            ->setUnitPriceAtPurchase(6)
            ;
        $purchaseItems[1] = (new PurchaseItem())
            ->setPurchase($purchases[0])
            ->setProduct($products[12])
            ->setQuantity(3)
            ->setUnitPriceAtPurchase(8.75)
            ;
        $purchaseItems[2] = (new PurchaseItem())
            ->setPurchase($purchases[0])
            ->setProduct($products[15])
            ->setQuantity(6)
            ->setUnitPriceAtPurchase(2.45)
            ;

        $io->text('Persisting purchase items ...');
        foreach ($purchaseItems as $purchaseItem) {
            $this->em->persist($purchaseItem);
            $entityCounter++;
        }

        ////////////////////
        /// MOBILE SALES ///
        ////////////////////

        /**
         * @var MobileSale[]
         */
        $mobileSales = [];

        $mobileSales[0] = (new MobileSale())
            ->setUuid($this->randomUuidFactory->create())
            ->setClient($clients[0])
            ->setTotal(2.2)
            ->setPaid(true)
            ->setCreatedAt(new DateTimeImmutable('2026-01-15 13:27:32'))
            ->setUpdatedAt(new DateTimeImmutable('2026-01-15 14:09:59'))
            ;
        $mobileSales[1] = (new MobileSale())
            ->setUuid($this->randomUuidFactory->create())
            ->setClient($clients[0])
            ->setTotal(10.25)
            ->setPaid(false)
            ->setCreatedAt(new DateTimeImmutable('2026-01-16 13:27:32'))
            ->setUpdatedAt(new DateTimeImmutable('2026-01-16 15:15:21'))
            ;
        $mobileSales[2] = (new MobileSale())
            ->setUuid($this->randomUuidFactory->create())
            ->setClient($clients[1])
            ->setTotal(4.65)
            ->setPaid(true)
            ->setCreatedAt(new DateTimeImmutable('2026-01-23 08:10:12'))
            ->setUpdatedAt(new DateTimeImmutable('2026-01-23 08:40:50'))
            ;

        $io->text('Persisting mobile sales ...');
        foreach ($mobileSales as $mobileSale) {
            $this->em->persist($mobileSale);
            $entityCounter++;
        }

        /////////////////////////
        /// MOBILE SALE ITEMS ///
        /////////////////////////

        /**
         * @var MobileSaleItem[]
         */
        $mobileSaleItems = [];

        $mobileSaleItems[0] = (new MobileSaleItem())
            ->setMobileSale($mobileSales[0])
            ->setProduct($products[5])
            ->setQuantity(2)
            ->setUnitPriceAtSale(1.1)
            ;
        $mobileSaleItems[1] = (new MobileSaleItem())
            ->setMobileSale($mobileSales[1])
            ->setProduct($products[13])
            ->setQuantity(3)
            ->setUnitPriceAtSale(.45)
            ;
        $mobileSaleItems[2] = (new MobileSaleItem())
            ->setMobileSale($mobileSales[1])
            ->setProduct($products[16])
            ->setQuantity(1)
            ->setUnitPriceAtSale(8.9)
            ;
        $mobileSaleItems[3] = (new MobileSaleItem())
            ->setMobileSale($mobileSales[2])
            ->setProduct($products[8])
            ->setQuantity(2)
            ->setUnitPriceAtSale(1.85)
            ;
        $mobileSaleItems[4] = (new MobileSaleItem())
            ->setMobileSale($mobileSales[2])
            ->setProduct($products[3])
            ->setQuantity(1)
            ->setUnitPriceAtSale(0.95)
            ;

        $io->text('Persisting mobile sale items ...');
        foreach ($mobileSaleItems as $mobileSaleItem) {
            $this->em->persist($mobileSaleItem);
            $entityCounter++;
        }

        // persist all entities
        $io->text('Flushing to database ...');
        $this->em->flush();
        $io->success("Database seeded - {$entityCounter} entities added");
        return Command::SUCCESS;
    }
}

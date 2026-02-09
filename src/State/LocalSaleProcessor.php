<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\LocalSale;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Uid\Factory\RandomBasedUuidFactory;
use Symfony\Component\Uid\Factory\UuidFactory;

class LocalSaleProcessor implements ProcessorInterface {

    private RandomBasedUuidFactory $randomUuidFactory;

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private LoggerInterface $logger,
        UuidFactory $uuidFactory,
        private Security $security,
    ) {
        $this->randomUuidFactory = $uuidFactory->randomBased();
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed {
        if (!($data instanceof LocalSale)) {
            throw new RuntimeException('LocalSaleProcessor should only be used for LocalSale objects');
        }
        $this->logger->info('LocalSaleProcessor called');
        $this->logger->info('number of local sale items: ' . \count($data->getLocalSaleItems()));
        // set the employee as the authenticated user
        $data->setEmployee($this->security->getUser());
        // create a new UUID for this sale
        $data->setUuid($this->randomUuidFactory->create());
        // compute the total (untaxed) of this sale
        $total = 0.;
        foreach ($data->getLocalSaleItems() as $item) {
            $this->logger->info('item: ' . $item->getProduct()->getLabel() . ' x ' . $item->getQuantity() . ' at ' . $item->getProduct()->getUnitPrice() . ' each');
            $item->setUnitPriceAtSale($item->getProduct()->getUnitPrice());
            $total += $item->getQuantity() * $item->getUnitPriceAtSale();
            // remove the quantity sold from the product inventory
            $previous_inventory = $item->getProduct()->getInventory();
            $new_inventory = $previous_inventory - $item->getQuantity();
            $item->getProduct()->setInventory($new_inventory);
        }
        $data->setTotal($total);
        // throw new HttpException(501, 'not implemented yet');
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}

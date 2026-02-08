<?php

namespace App\Controller\Api;

use App\Entity\LocalSale;
use App\Entity\MobileSale;
use App\Repository\LocalSaleRepository;
use App\Repository\MobileSaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class TicketItemsController extends AbstractController
{
    #[Route('/api/tickets/local-sale/{uuid}/items', name: 'api_ticket_local_sale_items', methods: ['GET'])]
    public function localSaleItems(string $uuid, LocalSaleRepository $localSaleRepository): JsonResponse
    {
        $uuidValue = $this->parseUuid($uuid);
        if ($uuidValue === null) {
            return $this->json(['message' => 'Invalid UUID.'], 400);
        }

        $localSale = $localSaleRepository->findOneBy(['uuid' => $uuidValue]);
        if (!$localSale) {
            return $this->json(['message' => 'Local sale not found.'], 404);
        }

        return $this->json($this->buildLocalSalePayload($localSale));
    }

    #[Route('/api/tickets/mobile-sale/{uuid}/items', name: 'api_ticket_mobile_sale_items', methods: ['GET'])]
    public function mobileSaleItems(string $uuid, MobileSaleRepository $mobileSaleRepository): JsonResponse
    {
        $uuidValue = $this->parseUuid($uuid);
        if ($uuidValue === null) {
            return $this->json(['message' => 'Invalid UUID.'], 400);
        }

        $mobileSale = $mobileSaleRepository->findOneBy(['uuid' => $uuidValue]);
        if (!$mobileSale) {
            return $this->json(['message' => 'Mobile sale not found.'], 404);
        }

        return $this->json($this->buildMobileSalePayload($mobileSale));
    }

    private function parseUuid(string $uuid): ?Uuid
    {
        try {
            return Uuid::fromString($uuid);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    private function buildLocalSalePayload(LocalSale $localSale): array
    {
        $items = [];
        foreach ($localSale->getLocalSaleItems() as $item) {
            $product = $item->getProduct();
            $quantity = $item->getQuantity() ?? 0.0;
            $unitPrice = $item->getUnitPriceAtSale() ?? 0.0;

            $items[] = [
                'product' => [
                    'code' => $product?->getCode(),
                    'label' => $product?->getLabel(),
                ],
                'quantity' => $quantity,
                'unitPriceAtSale' => $unitPrice,
                'lineTotal' => $quantity * $unitPrice,
            ];
        }

        return [
            'uuid' => (string) $localSale->getUuid(),
            'type' => 'local-sale',
            'total' => $localSale->getTotal(),
            'createdAt' => $localSale->getCreatedAt()?->format(\DateTimeInterface::RFC3339_EXTENDED),
            'updatedAt' => $localSale->getUpdatedAt()?->format(\DateTimeInterface::RFC3339_EXTENDED),
            'items' => $items,
        ];
    }

    private function buildMobileSalePayload(MobileSale $mobileSale): array
    {
        $items = [];
        foreach ($mobileSale->getMobileSaleItems() as $item) {
            $product = $item->getProduct();
            $quantity = $item->getQuantity() ?? 0.0;
            $unitPrice = $item->getUnitPriceAtSale() ?? 0.0;

            $items[] = [
                'product' => [
                    'code' => $product?->getCode(),
                    'label' => $product?->getLabel(),
                ],
                'quantity' => $quantity,
                'unitPriceAtSale' => $unitPrice,
                'lineTotal' => $quantity * $unitPrice,
            ];
        }

        return [
            'uuid' => (string) $mobileSale->getUuid(),
            'type' => 'mobile-sale',
            'total' => $mobileSale->getTotal(),
            'paid' => $mobileSale->isPaid(),
            'createdAt' => $mobileSale->getCreatedAt()?->format(\DateTimeInterface::RFC3339_EXTENDED),
            'updatedAt' => $mobileSale->getUpdatedAt()?->format(\DateTimeInterface::RFC3339_EXTENDED),
            'items' => $items,
        ];
    }
}

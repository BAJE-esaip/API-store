<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AuthenticatedClientProvider implements ProviderInterface {
    public function __construct(
        private Security $security,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
        // Only handle the Client entity
        if ($operation->getClass() !== Client::class) {
            return null;
        }
        return $this->security->getUser();
    }
}

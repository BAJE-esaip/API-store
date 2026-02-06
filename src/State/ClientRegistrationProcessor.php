<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Client;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Factory\RandomBasedUuidFactory;
use Symfony\Component\Uid\Factory\UuidFactory;

class ClientRegistrationProcessor implements ProcessorInterface {
    private RandomBasedUuidFactory $randomUuidFactory;

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher,
        UuidFactory $uuidFactory,
    ) {
        $this->randomUuidFactory = $uuidFactory->randomBased();
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed {
        if (!($data instanceof Client)) {
            throw new RuntimeException('ClientRegistrationProcessor should only be used for Client objects');
        }
        $hashedPassword = $this->passwordHasher->hashPassword(
            $data,
            $data->getPassword(),
        );
        $data->setPassword($hashedPassword);
        $data->setUuid($this->randomUuidFactory->create());
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}

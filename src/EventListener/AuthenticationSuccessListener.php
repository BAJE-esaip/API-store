<?php

namespace App\EventListener;

use App\Entity\Client;
use App\Entity\Employee;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class AuthenticationSuccessListener {
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    #[AsEventListener(event: 'lexik_jwt_authentication.on_authentication_success')]
    public function onAuthenticationSuccess($event): void {
        $this->logger->debug('onAuthenticationSuccess called');
        $user = $event->getUser();
        $jsonData = $event->getData();
        // $data['user'] = [
        //     'id' => $user->getId(),
        //     'username' => $user->getUsername(),
        // ];
        if ($user instanceof Employee) {
            // add employee data
            $jsonData['employee'] = [
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'roles' => $user->getRoles(),
            ];
            // ...
        }
        else if ($user instanceof Client) {
            // add client data
            $jsonData['client'] = [
                'email' => $user->getEmail(),
                'uuid' => $user->getUuid(),
                'createdAt' => $user->getCreatedAt()->format(\DateTimeInterface::RFC3339_EXTENDED),
            ];
        }
        else {
            $this->logger->error('The authenticated user is not an instance of Client or Employee');
        }
        $event->setData($jsonData);
    }
}

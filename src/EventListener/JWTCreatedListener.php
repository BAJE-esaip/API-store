<?php

namespace App\EventListener;

use App\Entity\Employee;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class JWTCreatedListener {
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    #[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created')]
    public function onJWTCreatedEvent(JWTCreatedEvent $event): void {
        $this->logger->info('onJWTCreatedEvent called');
        $user = $event->getUser();
        $payload = $event->getData();
        // add a user type claim for debug
        $payload['user_type'] = ($user instanceof Employee) ? 'employee' : 'client';
        $event->setData($payload);
    }
}

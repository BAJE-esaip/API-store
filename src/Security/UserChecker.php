<?php

namespace App\Security;

use App\Entity\Client;
use App\Entity\Employee;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface {
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function checkPreAuth(UserInterface $user): void {
        $this->logger->debug('UserChecker::checkPreAuth called');
        if (!($user instanceof Employee || $user instanceof Client)) {
            $this->logger->debug('$user is neither an Employee or a Client instance');
            return;
        }
        $deleted_at = $user->getDeletedAt()?->format('Y-m-d H:i:s');
        $this->logger->debug("deleted_at: {$deleted_at}");
        if ($user->getDeletedAt() !== null) {
            throw new CustomUserMessageAuthenticationException('Your account has been deleted and can no longer be accessed.');
        }

        if ($user instanceof Employee) {
            $roles = $user->getRoles();
            if (!in_array('ROLE_CONTROL', $roles, true)) {
                throw new CustomUserMessageAuthenticationException('Accès non autorisé.');
            }
        }
    }

    public function checkPostAuth(UserInterface $user): void {}
}

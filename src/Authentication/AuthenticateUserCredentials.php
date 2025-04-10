<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Authentication;

use Cspray\AnnotatedContainer\Attribute\Inject;
use Cspray\AnnotatedContainer\Attribute\Service;
use Psr\Log\LoggerInterface;

#[Service]
final readonly class AuthenticateUserCredentials {

    public function __construct(
        #[Inject('app.adminUsername', from: 'config')]
        private string $username,
        #[Inject('app.adminPasswordHash', from: 'config')]
        private string $passwordHash,
    ) {}

    public function authenticate(string $user, string $password) : bool {
        return hash_equals($this->username, $user)
            && password_verify($password, $this->passwordHash);
    }

}
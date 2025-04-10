<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Database;

use Cspray\AnnotatedContainer\Attribute\Inject;
use Cspray\AnnotatedContainer\Attribute\Service;

#[Service]
final readonly class DatabaseConfig {

    /**
     * @param int<1, max> $poolConnectionLimit
     */
    public function __construct(
        #[Inject('database.database', from: 'config')]
        public string $database,

        #[Inject('database.schema', from: 'config')]
        public string $schema,

        #[Inject('database.host', from: 'config')]
        public string $host,

        #[Inject('database.port', from: 'config')]
        public int $port,

        #[Inject('database.user', from: 'config')]
        public string $user,

        #[Inject('database.password', from: 'config')]
        public string $password,

        #[Inject('database.poolConnectionLimit', from: 'config')]
        public int $poolConnectionLimit
    ) {}

}

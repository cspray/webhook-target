<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests;

use Amp\Postgres\PostgresConnection;
use Cspray\WebhookTarget\Tests\Helper\ContainerHelper;
use Cspray\AnnotatedContainer\AnnotatedContainer;
use Cspray\DatabaseTestCase\AmpPostgresConnectionAdapter;
use Cspray\DatabaseTestCase\ConnectionAdapter;
use Cspray\DatabaseTestCase\DatabaseTestCase as DbTestCase;

abstract class DatabaseTestCase extends DbTestCase {

    private static AnnotatedContainer $container;

    protected static function getConnectionAdapter() : ConnectionAdapter {
        self::$container = ContainerHelper::bootstrapTestContainer(['default', static::getTestProfile(), 'web', 'docker']);
        return AmpPostgresConnectionAdapter::existingConnection(self::$container->get(PostgresConnection::class));
    }

    protected function getContainer() : AnnotatedContainer {
        return self::$container;
    }

    protected static function getTestProfile() : string {
        return 'test';
    }
}
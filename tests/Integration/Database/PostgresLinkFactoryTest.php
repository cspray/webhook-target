<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Integration\Database;

use Amp\Postgres\PostgresLink;
use Cspray\WebhookTarget\Database\DatabaseConfig;
use Cspray\WebhookTarget\Database\PostgresConnectionFactory;
use Cspray\WebhookTarget\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PostgresConnectionFactory::class)]
#[CoversClass(DatabaseConfig::class)]
final class PostgresLinkFactoryTest extends DatabaseTestCase {

    public function testPostgresLinkHasCorrectSchemaSet() : void {
        $config = new DatabaseConfig(
            'postgres',
            'web_app_test',
            'database',
            5432,
            'postgres',
            'password',
            1
        );
        $postgres = PostgresConnectionFactory::createPostgresConnection($config);

        self::assertInstanceOf(PostgresLink::class, $postgres);
        self::assertSame(
            'web_app_test',
            $postgres->query('SHOW search_path')->fetchRow()['search_path']
        );
    }


}
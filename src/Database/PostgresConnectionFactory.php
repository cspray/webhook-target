<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Database;

use Amp\Cancellation;
use Amp\Postgres\DefaultPostgresConnector;
use Amp\Postgres\PostgresConfig;
use Amp\Postgres\PostgresConnection;
use Amp\Postgres\PostgresConnectionPool;
use Amp\Sql\SqlConfig;
use Amp\Sql\SqlConnection;
use Amp\Sql\SqlConnector;
use Cspray\AnnotatedContainer\Attribute\ServiceDelegate;
use Override;

final class PostgresConnectionFactory {
    #[ServiceDelegate]
    public static function createPostgresConnection(DatabaseConfig $databaseConfig) : PostgresConnection {
        $config = PostgresConfig::fromString(sprintf(
            'database=%s host=%s port=%d user=%s password=%s',
            $databaseConfig->database,
            $databaseConfig->host,
            $databaseConfig->port,
            $databaseConfig->user,
            $databaseConfig->password
        ));

        return new PostgresConnectionPool(
            $config,
            maxConnections: $databaseConfig->poolConnectionLimit,
            resetConnections: false,
            connector: self::schemaSettingConnector(new DefaultPostgresConnector(), $databaseConfig)
        );
    }

    /**
     * @param SqlConnector<PostgresConfig, PostgresConnection> $connector
     * @return SqlConnector<PostgresConfig, PostgresConnection>
     */
    private static function schemaSettingConnector(SqlConnector $connector, DatabaseConfig $databaseConfig) : SqlConnector {
        /**
         * @implements SqlConnector<PostgresConfig, PostgresConnection>
         */
        return new class($connector, $databaseConfig->schema) implements SqlConnector {

            public function __construct(
                private readonly SqlConnector $connector,
                private readonly string $searchPath
            ) {}

            #[Override]
            public function connect(SqlConfig $config, ?Cancellation $cancellation = null) : SqlConnection {
                $link = $this->connector->connect($config, $cancellation);
                $link->query('DISCARD ALL');
                $link->query(sprintf('SET search_path TO %s', $this->searchPath));
                return $link;
            }
        };
    }
}

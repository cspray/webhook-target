<?php declare(strict_types=1);

use Cspray\WebhookTarget\ApplicationBootstrap;
use Cspray\WebhookTarget\Database\DatabaseConfig;
use Cspray\AnnotatedContainer\Profiles;
use Doctrine\DBAL\DriverManager;

$profiles = getenv('PROFILES');
if ($profiles === false) {
    throw new RuntimeException(
        'You MUST provide a comma-separated list of profiles to use when running migrations.'
    );
}
$profiles = Profiles::fromCommaDelimitedString($profiles);

$container = (new ApplicationBootstrap())->bootstrapContainer($profiles);
$dbConfig = $container->get(DatabaseConfig::class);

assert($dbConfig instanceof DatabaseConfig);

$connection = DriverManager::getConnection([
    'dbname' => $dbConfig->database,
    'user' => $dbConfig->user,
    'password' => $dbConfig->password,
    'host' => $dbConfig->host,
    'driver' => 'pgsql',
]);
$connection->executeQuery(sprintf('SET search_path TO %s', $dbConfig->schema));
return $connection;
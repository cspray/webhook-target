<?php declare(strict_types=1);

use Labrador\Util\RequiredEnvironmentVariable;

return [
    'database' => 'postgres',
    'schema' => RequiredEnvironmentVariable::get('DATABASE_SCHEMA'),
    'host' => RequiredEnvironmentVariable::get('DATABASE_HOST'),
    'port' => (int) RequiredEnvironmentVariable::get('DATABASE_PORT'),
    'user' => RequiredEnvironmentVariable::get('DATABASE_USER'),
    'password' => RequiredEnvironmentVariable::get('DATABASE_PASSWORD'),
    'poolConnectionLimit' => (int) RequiredEnvironmentVariable::get('DATABASE_CONNECTION_LIMIT'),
];
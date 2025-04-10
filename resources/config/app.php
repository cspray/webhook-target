<?php declare(strict_types=1);

use Labrador\Util\RequiredEnvironmentVariable;

$hasTlsCert = file_exists(dirname(__DIR__) . '/certs/tls.pem');

return [
    'templateDir' => dirname(__DIR__) . '/templates',
    'adminUsername' => RequiredEnvironmentVariable::get('ADMIN_USERNAME'),
    'adminPasswordHash' => RequiredEnvironmentVariable::get('ADMIN_PASSWORD')
];
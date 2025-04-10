<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Autowire;

use Amp\Postgres\PostgresConnection;
use Amp\Postgres\PostgresLink;
use Cspray\AnnotatedContainer\StaticAnalysis\DefinitionProvider as AnnotatedContainerDefinitionProvider;
use Cspray\AnnotatedContainer\StaticAnalysis\DefinitionProviderContext;
use League\Plates\Engine;
use Override;
use function Cspray\AnnotatedContainer\Definition\service;
use function Cspray\AnnotatedContainer\Reflection\types;

final class DefinitionProvider implements AnnotatedContainerDefinitionProvider {

    #[Override]
    public function consume(DefinitionProviderContext $context) : void {
        $context->addServiceDefinition(service(types()->class(Engine::class)));
        $context->addServiceDefinition(service(types()->class(PostgresConnection::class)));
    }

}

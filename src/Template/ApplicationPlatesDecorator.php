<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Template;

use Cspray\WebhookTarget\ApplicationConfig;
use Cspray\AnnotatedContainer\Attribute\Service;
use Cspray\AnnotatedContainer\Attribute\ServiceDelegate;
use Labrador\Template\Plates\PlatesEngineDecorator;
use League\Plates\Engine;
use Override;

#[Service]
final class ApplicationPlatesDecorator implements PlatesEngineDecorator {

    public function __construct(
        private readonly ApplicationConfig $config
    ) {}

    #[Override]
    public function decorate(Engine $engine) : void {
        $engine->addFolder('layouts', $this->config->templateDir . '/layouts');
        $engine->addFolder('pages', $this->config->templateDir . '/pages');
        $engine->addFolder('components', $this->config->templateDir . '/components');
    }
}
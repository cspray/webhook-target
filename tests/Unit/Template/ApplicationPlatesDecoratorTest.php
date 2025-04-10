<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Unit\Template;

use Cspray\WebhookTarget\ApplicationConfig;
use Cspray\WebhookTarget\Template\ApplicationPlatesDecorator;
use League\Plates\Engine as TemplateEngine;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[
    CoversClass(ApplicationPlatesDecorator::class),
    CoversClass(ApplicationConfig::class)
]
final class ApplicationPlatesDecoratorTest extends TestCase {

    private ApplicationConfig $config;
    private ApplicationPlatesDecorator $subject;
    private TemplateEngine $engine;

    protected function setUp() : void {
        $this->config = new ApplicationConfig(
            dirname(__DIR__, 3) . '/resources/templates',
        );
        $this->subject = new ApplicationPlatesDecorator($this->config);
        $this->engine = new TemplateEngine();
    }

    public function testPlatesEngineHasLayoutsFolder() : void {
        $this->subject->decorate($this->engine);

        $folders = $this->engine->getFolders();

        self::assertTrue($folders->exists('layouts'));
    }

    public function testPlatesEngineHasComponentsFolder() : void {
        $this->subject->decorate($this->engine);

        $folders = $this->engine->getFolders();

        self::assertTrue($folders->exists('components'));
    }

    public function testPlatesEngineHasPagesFolder() : void {
        $this->subject->decorate($this->engine);

        $folders = $this->engine->getFolders();

        self::assertTrue($folders->exists('pages'));
    }

    public function testPlatesEngineHasCorrectLayoutsPaths() : void {
        $this->subject->decorate($this->engine);

        $folders = $this->engine->getFolders();

        self::assertSame(
            $this->config->templateDir . '/layouts',
            $folders->get('layouts')->getPath()
        );
    }

    public function testPlatesEngineHasCorrectComponentsPaths() : void {
        $this->subject->decorate($this->engine);

        $folders = $this->engine->getFolders();

        self::assertSame(
            $this->config->templateDir . '/components',
            $folders->get('components')->getPath()
        );
    }

    public function testPlatesEngineHasCorrectPagesPaths() : void {
        $this->subject->decorate($this->engine);

        $folders = $this->engine->getFolders();

        self::assertSame(
            $this->config->templateDir . '/pages',
            $folders->get('pages')->getPath()
        );
    }

}

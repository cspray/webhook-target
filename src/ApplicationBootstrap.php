<?php declare(strict_types=1);

namespace Cspray\WebhookTarget;

use Cspray\AnnotatedContainer\AnnotatedContainer;
use Cspray\AnnotatedContainer\Bootstrap\Bootstrap;
use Cspray\AnnotatedContainer\Bootstrap\DefaultParameterStoreFactory;
use Cspray\AnnotatedContainer\Bootstrap\DelegatedParameterStoreFactory;
use Cspray\AnnotatedContainer\Bootstrap\ParameterStoreFactory;
use Cspray\AnnotatedContainer\ContainerFactory\PhpDiContainerFactory;
use Cspray\AnnotatedContainer\Event\Emitter;
use Cspray\AnnotatedContainer\Profiles;
use Cspray\AnnotatedContainer\Secrets\ConfigParameterStoreFactory;
use Cspray\AnnotatedContainer\Secrets\IdentifierSourceMap;
use Cspray\AnnotatedContainer\Secrets\PhpIncludeValueProvider;
use Cspray\AnnotatedContainer\Secrets\ProfileAwareSource;
use Cspray\AnnotatedContainer\Secrets\SingleValueProviderSource;
use Labrador\AsyncEvent\Autowire\RegisterAutowiredListener;
use Labrador\Web\Autowire\RegisterControllerListener;

/**
 * Responsible for configuring and bootstrapping the Annotated Container for your web application.
 *
 * Generally speaking, this class should be restricted to what is required by Annotated Container to create and run its
 * bootstrap. That should be things like, adding new Listeners to the Emitter and editing the configuration values that
 * are available. Please check the corresponding methods below for how to adjust these aspects of your app's
 * bootstrap.
 *
 * If you find yourself writing an app that requires something beyond configuring Annotated Container, it is
 * recommended you encapsulate that logic in its own class, pass it as a dependency in app.php, and execute the
 * appropriate code in `bootstrapContainer`.
 */
final readonly class ApplicationBootstrap {

    public function __construct() {}

    public function bootstrapContainer(Profiles $profiles) : AnnotatedContainer {
        $emitter = $this->emitter();
        $parameterStoreFactory = $this->configParameterStoreFactory($profiles);
        return Bootstrap::fromAnnotatedContainerConventions(
            new PhpDiContainerFactory($emitter),
            $emitter,
            $parameterStoreFactory
        )->bootstrapContainer($profiles);
    }

    /**
     * Add Annotated Container Listeners, so you can respond to events that happen during the creation of your
     * container.
     *
     * @return Emitter
     * @throws \Cspray\AnnotatedContainer\Exception\InvalidListener
     */
    private function emitter() : Emitter {
        $emitter = new Emitter();

        // Ensure Controllers attributed with #[HttpController] are added to the router
        $emitter->addListener(new RegisterControllerListener());
        // Ensure AsyncEvent Listeners attributed with #[AutowiredListeners] are registered to the Async Event Emitter
        $emitter->addListener(new RegisterAutowiredListener());

        return $emitter;
    }

    private function configParameterStoreFactory(Profiles $profiles) : ParameterStoreFactory {
        $configDir = dirname(__DIR__) . '/resources/config';
        $databaseValueProvider = new PhpIncludeValueProvider($configDir . '/database.php');
        $factory = new ConfigParameterStoreFactory(
            (new IdentifierSourceMap())->withIdentifierAndSources('config', [
                new ProfileAwareSource('database', $profiles->toArray(), [
                    'dev' => $databaseValueProvider,
                    'prod' => $databaseValueProvider,
                    'test' => new PhpIncludeValueProvider($configDir . '/database.test.php'),
                ]),
                new SingleValueProviderSource('app', new PhpIncludeValueProvider($configDir . '/app.php')),
                new SingleValueProviderSource('server', new PhpIncludeValueProvider($configDir . '/server.php'))
            ])
        );
        $parameterStoreFactory = new DelegatedParameterStoreFactory(new DefaultParameterStoreFactory());
        $parameterStoreFactory->addParameterStoreFactory('config', $factory);

        return $parameterStoreFactory;
    }

}
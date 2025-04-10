<?php declare(strict_types=1);

use Cspray\WebhookTarget\ApplicationBootstrap;
use Cspray\AnnotatedContainer\Profiles;
use Labrador\Web\Application\Application;
use function Amp\trapSignal;

(static function() {
    require_once __DIR__ . '/vendor/autoload.php';

    $profiles = getenv('PROFILES');
    if (!is_string($profiles)) {
        throw new RuntimeException('An environment variable named PROFILES must be provided with a comma-separated list of active profiles.');
    }
    $profiles = Profiles::fromCommaDelimitedString($profiles);

    $container = (new ApplicationBootstrap())->bootstrapContainer($profiles);
    $app = $container->get(Application::class);

    $app->start();

    trapSignal([SIGINT, SIGTERM]);

    $app->stop();
})();
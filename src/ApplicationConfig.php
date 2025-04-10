<?php declare(strict_types=1);

namespace Cspray\WebhookTarget;

use Amp\Http\Cookie\CookieAttributes;
use Amp\Http\Server\Session\LocalSessionStorage;
use Amp\Http\Server\Session\SessionFactory;
use Amp\Http\Server\Session\SessionMiddleware;
use Amp\Sync\LocalKeyedMutex;
use Cspray\AnnotatedContainer\Attribute\Inject;
use Cspray\AnnotatedContainer\Attribute\Service;
use Override;

#[Service(primary: true)]
final readonly class ApplicationConfig {

    public function __construct(
        #[Inject('app.templateDir', from: 'config')]
        public string $templateDir,
    ) {}

}

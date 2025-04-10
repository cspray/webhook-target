<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Http\Session;

use Amp\Http\Server\Session\SessionFactory;
use Amp\Http\Server\Session\SessionMiddleware;
use Cspray\AnnotatedContainer\Attribute\Service;
use Cspray\AnnotatedContainer\Attribute\ServiceDelegate;
use Labrador\Web\Session\SessionMiddlewareFactory;
use Override;

#[Service]
final class ApplicationSessionFactory implements SessionMiddlewareFactory {

    #[Override]
    #[ServiceDelegate]
    public function createSessionMiddleware() : SessionMiddleware {
        return new SessionMiddleware(
            new SessionFactory()
        );
    }
}
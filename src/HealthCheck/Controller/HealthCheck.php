<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\HealthCheck\Controller;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Labrador\Web\Autowire\HttpController;
use Labrador\Web\Controller\SelfDescribingController;
use Labrador\Web\Router\Mapping\GetMapping;
use Override;

#[HttpController(new GetMapping('/health-check'))]
final class HealthCheck extends SelfDescribingController {

    #[Override]
    public function handleRequest(Request $request) : Response {
        return new Response();
    }

}

<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\WebhookTarget\Controller;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Cspray\WebhookTarget\WebhookTarget\Controller\Formatter\RequestBodyFormatter;
use Cspray\WebhookTarget\WebhookTarget\Entity\ReceivedWebhook;
use Cspray\WebhookTarget\WebhookTarget\Repository\ReceivedWebhookRepository;
use Labrador\Web\Autowire\HttpController;
use Labrador\Web\Controller\SelfDescribingController;
use Labrador\Web\Router\Mapping\PostMapping;
use Ramsey\Uuid\Uuid;

#[HttpController(new PostMapping('/webhook/target'))]
final class ProcessWebhook extends SelfDescribingController {

    public function __construct(
        private readonly ReceivedWebhookRepository $repository,
    ) {}

    public function handleRequest(Request $request) : Response {
        $this->repository->save(
            new ReceivedWebhook(
                Uuid::uuid4(),
                $request->getProtocolVersion(),
                $request->getMethod(),
                $request->getUri(),
                $request->getHeaders(),
                $request->getBody()->read()
            )
        );

        return new Response(status: HttpStatus::NO_CONTENT);
    }
}
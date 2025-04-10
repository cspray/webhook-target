<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Home\Controller;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Labrador\Template\Plates\PlatesTemplateIdentifier;
use Labrador\Template\Renderer;
use Labrador\Web\Autowire\HttpController;
use Labrador\Web\Autowire\SessionAwareController;
use Labrador\Web\Controller\SelfDescribingController;
use Labrador\Web\Response\ResponseFactory;
use Labrador\Web\Router\Mapping\GetMapping;
use League\Plates\Engine as TemplateEngine;
use Override;

#[SessionAwareController(new GetMapping('/'))]
final class Home extends SelfDescribingController {

    public function __construct(
        private readonly Renderer $renderer,
        private readonly ResponseFactory $responseFactory,
    ) {}

    #[Override]
    public function handleRequest(Request $request) : Response {
        $templateIdentifier = PlatesTemplateIdentifier::folderTemplate('pages', 'home');

        return $this->responseFactory->html(
            $this->renderer->render($templateIdentifier, new HomeTemplateData())
        );
    }

}

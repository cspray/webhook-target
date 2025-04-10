<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Dashboard\Controller;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Cspray\WebhookTarget\Dashboard\Template\DashboardTemplateData;
use Cspray\WebhookTarget\Http\Session\SessionAttribute;
use Cspray\WebhookTarget\Http\Session\SessionHelper;
use Cspray\WebhookTarget\WebhookTarget\Repository\ReceivedWebhookRepository;
use Labrador\Template\Plates\PlatesTemplateIdentifier;
use Labrador\Template\Renderer;
use Labrador\Web\Autowire\SessionAwareController;
use Labrador\Web\Controller\SelfDescribingController;
use Labrador\Web\Response\ResponseFactory;
use Labrador\Web\Router\Mapping\GetMapping;
use League\Uri\Http;

#[SessionAwareController(new GetMapping('/dashboard'))]
final class Home extends SelfDescribingController {

     public function __construct(
         private readonly ReceivedWebhookRepository $repository,
         private readonly SessionHelper $sessionHelper,
         private readonly Renderer $renderer,
         private readonly ResponseFactory $responseFactory,
     ) {}

    public function handleRequest(Request $request) : Response {
         if ($this->sessionHelper->get($request, SessionAttribute::UserId) === null) {
             return $this->responseFactory->seeOther(Http::new('/'));
         }

         return $this->responseFactory->html(
             $this->renderer->render(
                 PlatesTemplateIdentifier::folderTemplate('pages', 'dashboard/home'),
                 new DashboardTemplateData($this->repository->fetchAll())
             )
         );
    }
}
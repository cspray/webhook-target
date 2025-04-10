<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Authentication\Controller;

use Amp\Http\HttpStatus;
use Amp\Http\Server\FormParser\FormParser;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Cspray\WebhookTarget\Authentication\AuthenticateUserCredentials;
use Cspray\WebhookTarget\Home\Controller\HomeTemplateData;
use Cspray\WebhookTarget\Http\Session\SessionAttribute;
use Cspray\WebhookTarget\Http\Session\SessionHelper;
use Labrador\Template\Plates\PlatesTemplateIdentifier;
use Labrador\Template\Renderer;
use Labrador\Web\Autowire\SessionAwareController;
use Labrador\Web\Controller\SelfDescribingController;
use Labrador\Web\Response\ResponseFactory;
use Labrador\Web\Router\Mapping\PostMapping;
use League\Uri\Http;

#[SessionAwareController(new PostMapping('/authenticate'))]
final class AuthenticateUser extends SelfDescribingController {

    public function __construct(
        private readonly AuthenticateUserCredentials $authenticateUserCredentials,
        private readonly SessionHelper $sessionHelper,
        private readonly Renderer $renderer,
        private readonly ResponseFactory $responseFactory,
    ) {}

    public function handleRequest(Request $request) : Response {
        $form = (new FormParser())->parseForm($request);
        $username = $form->getValue('username');
        $password = $form->getValue('password');
        if ($this->authenticateUserCredentials->authenticate($username, $password)) {
            $this->sessionHelper->set($request, SessionAttribute::UserId, $username);
            return $this->responseFactory->seeOther(Http::new('/dashboard'));
        }

        return $this->responseFactory->html(
            $this->renderer->render(
                PlatesTemplateIdentifier::folderTemplate('pages', 'home'),
                new HomeTemplateData(true)
            ),
            status: HttpStatus::FORBIDDEN
        );
    }
}
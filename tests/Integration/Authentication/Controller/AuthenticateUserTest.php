<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Integration\Authentication\Controller;

use Amp\Http\Client\Form;
use Amp\Http\HttpStatus;
use Amp\Http\Server\Driver\Client;
use Amp\Http\Server\Request;
use Cspray\WebhookTarget\Authentication\AuthenticateUserCredentials;
use Cspray\WebhookTarget\Authentication\Controller\AuthenticateUser;
use Cspray\WebhookTarget\Http\Session\SessionAttribute;
use Cspray\WebhookTarget\Http\Session\SessionHelper;
use Cspray\WebhookTarget\Tests\Helper\ContainerHelper;
use Labrador\Template\Renderer;
use Labrador\TestHelper\ControllerInvoker;
use Labrador\Web\Response\ResponseFactory;
use League\Uri\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

#[CoversClass(AuthenticateUser::class)]
class AuthenticateUserTest extends TestCase {

    private AuthenticateUser $subject;
    private Client&MockObject $client;

    protected function setUp() : void {
        $this->client = $this->createMock(Client::class);
        $container = ContainerHelper::bootstrapTestContainer(['default', 'test', 'docker', 'web']);
        $this->subject = new AuthenticateUser(
            new AuthenticateUserCredentials('my-user', password_hash('my-password', PASSWORD_DEFAULT)),
            new SessionHelper(),
            $container->get(Renderer::class),
            $container->get(ResponseFactory::class),
        );
    }

    private function request(Form $form) : Request {
        return new Request(
            $this->client,
            'POST',
            Http::new('/authenticate'),
            [

                'Content-Type' => $form->getContentType(), 'Content-Length' => $form->getContentLength()
            ],
            $form->getContent()
        );
    }

    public function testAuthenticateUserWithFormThatDoesNotHaveCorrectUsernameHasForbiddenResponse() : void {
        $form = new Form();
        $form->addField('username', 'wrong-username');
        $form->addField('password', 'wrong-password');

        $result = ControllerInvoker::withTestSessionMiddleware()->invokeController($this->request($form), $this->subject);

        self::assertSame(HttpStatus::FORBIDDEN, $result->response()->getStatus());

        $crawler = new Crawler($result->response()->getBody()->read(), 'https://example.com');

        $form = $crawler->filter('form')->form();

        self::assertSame('POST', $form->getMethod());
        self::assertSame('https://example.com/authenticate', $form->getUri());
        self::assertSame('Username', $form->get('username')->getLabel()->nodeValue);
        self::assertSame('Password', $form->get('password')->getLabel()->nodeValue);
        self::assertCount(1, $crawler->filter('form button[type="submit"]'));

        $message = $crawler->filter('form .message');
        self::assertCount(1, $message);
        self::assertSame('That username or password is incorrect', $message->text());
    }

    public function testAuthenticateUserWithFormDoesHaveCorrectUsernameButWrongPasswordHasForbiddenResponse() : void {
        $form = new Form();
        $form->addField('username', 'my-user');
        $form->addField('password', 'bad-password');
        $result = ControllerInvoker::withTestSessionMiddleware()->invokeController($this->request($form), $this->subject);

        self::assertSame(HttpStatus::FORBIDDEN, $result->response()->getStatus());

        $crawler = new Crawler($result->response()->getBody()->read(), 'https://example.com');

        $form = $crawler->filter('form')->form();

        self::assertSame('POST', $form->getMethod());
        self::assertSame('https://example.com/authenticate', $form->getUri());
        self::assertSame('Username', $form->get('username')->getLabel()->nodeValue);
        self::assertSame('Password', $form->get('password')->getLabel()->nodeValue);
        self::assertCount(1, $crawler->filter('form button[type="submit"]'));

        $message = $crawler->filter('form .message');
        self::assertCount(1, $message);
        self::assertSame('That username or password is incorrect', $message->text());
    }

    public function testAuthenticateUserWithCorrectUsernameAndPasswordRedirectsToDashboardAndSetsSessionData() : void {
        $form = new Form();
        $form->addField('username', 'my-user');
        $form->addField('password', 'my-password');
        $result = ControllerInvoker::withTestSessionMiddleware()->invokeController($this->request($form), $this->subject);

        self::assertSame(HttpStatus::SEE_OTHER, $result->response()->getStatus());
        self::assertSame('/dashboard', $result->response()->getHeader('Location'));

        $session = $result->readSession();

        self::assertArrayHasKey(SessionAttribute::UserId->name, $session);
        self::assertSame('my-user', $session[SessionAttribute::UserId->name]);
    }

}
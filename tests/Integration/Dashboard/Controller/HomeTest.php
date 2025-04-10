<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Integration\Dashboard\Controller;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Driver\Client;
use Amp\Http\Server\Request;
use Cspray\DatabaseTestCase\LoadFixture;
use Cspray\WebhookTarget\Dashboard\Controller\Home;
use Cspray\WebhookTarget\Http\Session\SessionAttribute;
use Cspray\WebhookTarget\Tests\DatabaseTestCase;
use Cspray\WebhookTarget\Tests\Fixture\Database\ReceivedWebhookOne;
use Cspray\WebhookTarget\Tests\Fixture\Database\ReceivedWebhookTwo;
use Labrador\TestHelper\ControllerInvoker;
use League\Uri\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DomCrawler\Crawler;

#[CoversClass(Home::class)]
class HomeTest extends DatabaseTestCase {

    private Home $subject;
    private Client&MockObject $client;

    protected function beforeEach() : void {
        $this->client = $this->createMock(Client::class);
        $this->subject = $this->getContainer()->get(Home::class);
    }

    public function testDashboardRedirectsToHomepageIfNoUserIdSet() : void {
        $request = new Request($this->client, 'GET', Http::new('/dashboard'));
        $result = ControllerInvoker::withTestSessionMiddleware()->invokeController($request, $this->subject);

        self::assertSame(HttpStatus::SEE_OTHER, $result->response()->getStatus());
        self::assertSame('/', $result->response()->getHeader('Location'));
    }

    #[LoadFixture(
        new ReceivedWebhookOne(),
        new ReceivedWebhookTwo()
    )]
    public function testDashboardShowsReceivedWebhooksIfUserIdSet() : void {
        $request = new Request($this->client, 'GET', Http::new('/dashboard'));
        $result = ControllerInvoker::withTestSessionMiddleware([
            SessionAttribute::UserId->name => 'my-username'
        ])->invokeController($request, $this->subject);

        self::assertSame(HttpStatus::OK, $result->response()->getStatus());

        $crawler = new Crawler($result->response()->getBody()->read(), 'https://example.com');

        self::assertCount(0, $crawler->filter('.message'));

        $webhooks = $crawler->filter('.received-webhook');

        self::assertCount(2, $webhooks);
    }

    public function testMessageShownIfNoReceivedWebhooksAreFound() : void {
        $request = new Request($this->client, 'GET', Http::new('/dashboard'));
        $result = ControllerInvoker::withTestSessionMiddleware([
            SessionAttribute::UserId->name => 'my-username'
        ])->invokeController($request, $this->subject);

        self::assertSame(HttpStatus::OK, $result->response()->getStatus());

        $crawler = new Crawler($result->response()->getBody()->read(), 'https://example.com');

        $message = $crawler->filter('.message');

        self::assertCount(1, $message);
        self::assertSame('No webhooks have been received yet.', $message->text());
    }

}
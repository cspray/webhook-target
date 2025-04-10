<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Integration\Home\Controller;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Driver\Client;
use Amp\Http\Server\Request;
use Cspray\WebhookTarget\Home\Controller\Home;
use Cspray\WebhookTarget\Tests\Helper\ContainerHelper;
use Labrador\TestHelper\ControllerInvoker;
use League\Uri\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

#[CoversClass(Home::class)]
class HomeTest extends TestCase {

    private Home $home;
    private Client&MockObject $client;

    protected function setUp() : void {
        $container = ContainerHelper::bootstrapTestContainer(['default', 'dev', 'test', 'docker']);
        $this->home = $container->get(Home::class);
        $this->client = $this->createMock(Client::class);
    }

    public function testHomeHasCorrectLoginForm() : void {
        $result = ControllerInvoker::withTestSessionMiddleware()
            ->invokeController(
                new Request($this->client, 'GET', Http::new('/login')),
                $this->home
            );

        self::assertSame(HttpStatus::OK, $result->response()->getStatus());

        $crawler = new Crawler($result->response()->getBody()->read(), uri: 'http://example.com');

        $form = $crawler->filter('form')->form();

        self::assertSame('POST', $form->getMethod());
        self::assertSame('http://example.com/authenticate', $form->getUri());
        self::assertSame('Username', $form->get('username')->getLabel()->nodeValue);
        self::assertSame('Password', $form->get('password')->getLabel()->nodeValue);
        self::assertCount(1, $crawler->filter('form button[type="submit"]'));

        self::assertCount(0, $crawler->filter('form .message'));
    }

}
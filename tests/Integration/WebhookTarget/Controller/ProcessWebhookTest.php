<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Integration\WebhookTarget\Controller;

use Amp\Http\HttpStatus;
use Amp\Http\Server\Driver\Client;
use Amp\Http\Server\Request;
use Cspray\WebhookTarget\Tests\DatabaseTestCase;
use Cspray\WebhookTarget\WebhookTarget\Controller\ProcessWebhook;
use Labrador\TestHelper\ControllerInvoker;
use League\Uri\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(ProcessWebhook::class)]
final class ProcessWebhookTest extends DatabaseTestCase {

    private ProcessWebhook $subject;
    private Client&MockObject $client;

    protected function beforeEach() : void {
        $this->client = $this->createMock(Client::class);
        $this->subject = $this->getContainer()->get(ProcessWebhook::class);
    }

    public function testProcessWebhookSavesRequestToTheRepository() : void {
        $request = new Request(
            $this->client,
            'GET',
            Http::new('https://example.com'),
            [
                'Header-One' => 'Foo',
                'Header-Two' => 'Bar',
                'Header-Three' => ['one', 'two', 'three']
            ],
            'The body of the request',
        );

        $table = $this->getTable('received_webhook');
        self::assertCount(0, $table);

        $result = ControllerInvoker::withTestSessionMiddleware()->invokeController($request, $this->subject);

        self::assertSame(HttpStatus::NO_CONTENT, $result->response()->getStatus());

        $table = $this->getTable('received_webhook');
        self::assertCount(1, $table);
        $row = $table->getRow(0);

        self::assertNotNull($row->get('id'));
        self::assertSame('1.1', $row->get('protocol'));
        self::assertSame('GET', $row->get('method'));
        self::assertSame('https://example.com', $row->get('uri'));
        self::assertSame(json_encode([
            'header-one' => ['Foo'],
            'header-two' => ['Bar'],
            'header-three' => ['one', 'two', 'three'],
            'cookie' => ['session=known-session-id-controller-invoker']
        ]), $row->get('headers'));
        self::assertSame('The body of the request', $row->get('body'));
        self::assertNotNull($row->get('created_at'));
        self::assertNotNull($row->get('updated_at'));
    }

}
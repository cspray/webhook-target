<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Integration\WebhookTarget\Repository;

use Cspray\DatabaseTestCase\LoadFixture;
use Cspray\WebhookTarget\Tests\DatabaseTestCase;
use Cspray\WebhookTarget\Tests\Fixture\Database\ReceivedWebhookOne;
use Cspray\WebhookTarget\Tests\Fixture\Database\ReceivedWebhookThree;
use Cspray\WebhookTarget\Tests\Fixture\Database\ReceivedWebhookTwo;
use Cspray\WebhookTarget\WebhookTarget\Entity\ReceivedWebhook;
use Cspray\WebhookTarget\WebhookTarget\Repository\PostgresBackedReceivedWebhookRepository;
use League\Uri\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use Ramsey\Uuid\Uuid;

#[CoversClass(PostgresBackedReceivedWebhookRepository::class)]
final class PostgresBackedReceivedWebhookRepositoryTest extends DatabaseTestCase {

    private PostgresBackedReceivedWebhookRepository $subject;

    protected function beforeEach() : void {
        $this->subject = new PostgresBackedReceivedWebhookRepository($this->getUnderlyingConnection());
    }

    public function testSaveReceivedWebhookCreatesCorrectEntryInDatabase() : void {
        $receivedWebhook = new ReceivedWebhook(
            $id = Uuid::uuid4(),
            '1.1',
            'GET',
            Http::new('https://example.com/webhook/target'),
            $headers = [
                'Header-One' => 'Foo',
                'Header-Two' => 'bar',
                'Header-Three' => ['One', 'Two', 'Three']
            ],
            'My response body'
        );

        $table = $this->getTable('received_webhook');
        self::assertCount(0, $table);

        $this->subject->save($receivedWebhook);

        $table = $this->getTable('received_webhook');
        self::assertCount(1, $table);

        $row = $table->getRow(0);
        self::assertSame($id->toString(), $row->get('id'));
        self::assertSame('1.1', $row->get('protocol'));
        self::assertSame('GET', $row->get('method'));
        self::assertSame('https://example.com/webhook/target', $row->get('uri'));
        self::assertSame(json_encode($headers), $row->get('headers'));
        self::assertSame('My response body', $row->get('body'));
    }

    public function testFetchAllWithNoRecordsReturnsEmptyGenerator() : void {
        self::assertSame(
            [],
            iterator_to_array($this->subject->fetchAll())
        );
    }

    #[LoadFixture(
        new ReceivedWebhookOne(),
        new ReceivedWebhookTwo(),
        new ReceivedWebhookThree()
    )]
    public function testFetchAllWithRecordsReturnsCorrectEntries() : void {
        $records = iterator_to_array($this->subject->fetchAll());

        self::assertCount(3, $records);
        self::assertContainsOnlyInstancesOf(ReceivedWebhook::class, $records);

        /** @var ReceivedWebhook $recordOne */
        $recordOne = $records[0];
        self::assertSame(ReceivedWebhookOne::ID, $recordOne->id->toString());
        self::assertSame('1.1', $recordOne->protocol);
        self::assertSame('GET', $recordOne->method);
        self::assertSame('https://one.example.com', (string) $recordOne->uri);
        self::assertSame([
            'Content-Type' => 'application/json',
            'Content-Length' => 100
        ], $recordOne->headers);
        self::assertSame(json_encode(['body' => 'content one']), $recordOne->body);
        self::assertNotNull($recordOne->createdAt);
        self::assertNotNull($recordOne->updatedAt);

        /** @var ReceivedWebhook $recordTwo */
        $recordTwo = $records[1];
        self::assertSame(ReceivedWebhookTwo::ID, $recordTwo->id->toString());
        self::assertSame('2.0', $recordTwo->protocol);
        self::assertSame('POST', $recordTwo->method);
        self::assertSame('https://two.example.com', (string) $recordTwo->uri);
        self::assertSame([
            'Content-Type' => 'application/json',
            'Content-Length' => 200
        ], $recordTwo->headers);
        self::assertSame(json_encode(['body' => 'content two']), $recordTwo->body);
        self::assertNotNull($recordTwo->createdAt);
        self::assertNotNull($recordTwo->updatedAt);

        /** @var ReceivedWebhook $recordThree */
        $recordThree = $records[2];
        self::assertSame(ReceivedWebhookThree::ID, $recordThree->id->toString());
        self::assertSame('1.1', $recordThree->protocol);
        self::assertSame('DELETE', $recordThree->method);
        self::assertSame('https://three.example.com', (string) $recordThree->uri);
        self::assertSame([
            'Content-Type' => 'application/json',
            'Content-Length' => 300
        ], $recordThree->headers);
        self::assertSame(json_encode(['body' => 'content three']), $recordThree->body);
        self::assertNotNull($recordThree->createdAt);
        self::assertNotNull($recordThree->updatedAt);
    }

}
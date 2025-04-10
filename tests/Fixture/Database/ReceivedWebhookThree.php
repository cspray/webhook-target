<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Fixture\Database;

use Cspray\DatabaseTestCase\Fixture;
use Cspray\DatabaseTestCase\FixtureRecord;
use Cspray\DatabaseTestCase\SingleRecordFixture;

class ReceivedWebhookThree implements Fixture {

    public const string ID = '72e7345c-1d7e-45ca-8697-3920350f78a8';

    public function getFixtureRecords() : array {
        return [
            new FixtureRecord(
                'received_webhook',
                [
                    'id' => self::ID,
                    'protocol' => '1.1',
                    'method' => 'DELETE',
                    'uri' => 'https://three.example.com',
                    'headers' => json_encode([
                        'Content-Type' => 'application/json',
                        'Content-Length' => 300
                    ]),
                    'body' => json_encode(['body' => 'content three'])
                ]
            )
        ];
    }
}
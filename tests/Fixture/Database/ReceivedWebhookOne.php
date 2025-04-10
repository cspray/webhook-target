<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Fixture\Database;

use Cspray\DatabaseTestCase\Fixture;
use Cspray\DatabaseTestCase\FixtureRecord;
use Cspray\DatabaseTestCase\SingleRecordFixture;

class ReceivedWebhookOne implements Fixture {

    public const string ID = 'e3da5066-7ae8-48ed-ae0c-da50eafe2895';

    public function getFixtureRecords() : array {
        return [
            new FixtureRecord(
                'received_webhook',
                [
                    'id' => self::ID,
                    'protocol' => '1.1',
                    'method' => 'GET',
                    'uri' => 'https://one.example.com',
                    'headers' => json_encode([
                        'Content-Type' => 'application/json',
                        'Content-Length' => 100
                    ]),
                    'body' => json_encode(['body' => 'content one'])
                ]
            )
        ];
    }
}
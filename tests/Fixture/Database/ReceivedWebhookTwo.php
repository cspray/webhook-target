<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Fixture\Database;

use Cspray\DatabaseTestCase\Fixture;
use Cspray\DatabaseTestCase\FixtureRecord;
use Cspray\DatabaseTestCase\SingleRecordFixture;

class ReceivedWebhookTwo implements Fixture {

    public const string ID = '7b2da115-6a57-4da3-a4e9-b2246cbfc6e4';

    public function getFixtureRecords() : array {
        return [
            new FixtureRecord(
                'received_webhook',
                [
                    'id' => self::ID,
                    'protocol' => '2.0',
                    'method' => 'POST',
                    'uri' => 'https://two.example.com',
                    'headers' => json_encode([
                        'Content-Type' => 'application/json',
                        'Content-Length' => 200
                    ]),
                    'body' => json_encode(['body' => 'content two'])
                ]
            )
        ];
    }
}
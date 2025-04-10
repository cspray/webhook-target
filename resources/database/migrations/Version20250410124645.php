<?php

declare(strict_types=1);

namespace Cspray\WebhookTarget\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250410124645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create received webhooks table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
        CREATE TABLE received_webhook (
            id uuid PRIMARY KEY,
            protocol varchar,
            method varchar,
            uri varchar,
            headers json,
            body text,
            created_at timestamp DEFAULT NOW(),
            updated_at timestamp DEFAULT NOW()
        )
        SQL);

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE received_webhook');

    }
}

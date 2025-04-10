<?php

declare(strict_types=1);

namespace Cspray\WebhookTarget\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230622235615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add trigger to set current time in updated_at column on record update.';
    }

    public function up(Schema $schema): void
    {
        $sql = <<<SQL
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ language 'plpgsql';
SQL;

        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        $sql = <<<SQL
DROP FUNCTION IF EXISTS update_updated_at_column();
SQL;

        $this->addSql($sql);
    }
}

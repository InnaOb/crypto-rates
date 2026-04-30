<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260430092146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create crypto_rates table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE crypto_rates (
                id          INT AUTO_INCREMENT NOT NULL,
                pair        VARCHAR(10)        NOT NULL,
                rate        DECIMAL(18, 8)     NOT NULL,
                recorded_at DATETIME           NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');

        $this->addSql('
            CREATE INDEX idx_pair_recorded_at ON crypto_rates (pair, recorded_at)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE crypto_rates');
    }
}

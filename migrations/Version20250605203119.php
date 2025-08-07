<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250605203119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, guest_number SMALLINT NOT NULL, order_date DATE NOT NULL, allergy VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, restaurant_id INT NOT NULL, INDEX IDX_E00CEDDEB1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE restaurant CHANGE am_opening_time am_opening_time JSON NOT NULL, CHANGE pm_opening_time pm_opening_time JSON NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEB1E7706E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE booking
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE restaurant CHANGE am_opening_time am_opening_time LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE pm_opening_time pm_opening_time LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`
        SQL);
    }
}

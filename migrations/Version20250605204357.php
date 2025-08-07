<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250605204357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE menu_category (id INT AUTO_INCREMENT NOT NULL, menu_id_id INT NOT NULL, category_id_id INT NOT NULL, INDEX IDX_2A1D5C57EEE8BD30 (menu_id_id), INDEX IDX_2A1D5C579777D11E (category_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE menu_category ADD CONSTRAINT FK_2A1D5C57EEE8BD30 FOREIGN KEY (menu_id_id) REFERENCES menu (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE menu_category ADD CONSTRAINT FK_2A1D5C579777D11E FOREIGN KEY (category_id_id) REFERENCES category (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE menu_category DROP FOREIGN KEY FK_2A1D5C57EEE8BD30
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE menu_category DROP FOREIGN KEY FK_2A1D5C579777D11E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE menu_category
        SQL);
    }
}

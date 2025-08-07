<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250605204652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE food_category (id INT AUTO_INCREMENT NOT NULL, food_id_id INT NOT NULL, category_id_id INT NOT NULL, INDEX IDX_2E013E838E255BBD (food_id_id), INDEX IDX_2E013E839777D11E (category_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_category ADD CONSTRAINT FK_2E013E838E255BBD FOREIGN KEY (food_id_id) REFERENCES food (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_category ADD CONSTRAINT FK_2E013E839777D11E FOREIGN KEY (category_id_id) REFERENCES category (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E838E255BBD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_category DROP FOREIGN KEY FK_2E013E839777D11E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE food_category
        SQL);
    }
}

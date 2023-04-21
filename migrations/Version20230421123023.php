<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230421123023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_basket DROP INDEX UNIQ_4A7020D71BE1FB52, ADD INDEX IDX_4A7020D71BE1FB52 (basket_id)');
        $this->addSql('ALTER TABLE content_basket CHANGE basket_id basket_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_basket DROP INDEX IDX_4A7020D71BE1FB52, ADD UNIQUE INDEX UNIQ_4A7020D71BE1FB52 (basket_id)');
        $this->addSql('ALTER TABLE content_basket CHANGE basket_id basket_id INT NOT NULL');
    }
}

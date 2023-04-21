<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230421133918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket CHANGE purchase_date purchase_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE content_basket ADD products_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE content_basket ADD CONSTRAINT FK_4A7020D76C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_4A7020D76C8A81A9 ON content_basket (products_id)');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD2B06B351');
        $this->addSql('DROP INDEX IDX_D34A04AD2B06B351 ON product');
        $this->addSql('ALTER TABLE product DROP content_basket_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket CHANGE purchase_date purchase_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE content_basket DROP FOREIGN KEY FK_4A7020D76C8A81A9');
        $this->addSql('DROP INDEX IDX_4A7020D76C8A81A9 ON content_basket');
        $this->addSql('ALTER TABLE content_basket DROP products_id');
        $this->addSql('ALTER TABLE product ADD content_basket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD2B06B351 FOREIGN KEY (content_basket_id) REFERENCES content_basket (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D34A04AD2B06B351 ON product (content_basket_id)');
    }
}

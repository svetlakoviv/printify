<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200307191926 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product ADD product_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD14959723 FOREIGN KEY (product_type_id) REFERENCES product_type (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD14959723 ON product (product_type_id)');
        $this->addSql('ALTER TABLE product_type DROP FOREIGN KEY FK_13675884584665A');
        $this->addSql('DROP INDEX IDX_13675884584665A ON product_type');
        $this->addSql('ALTER TABLE product_type DROP product_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD14959723');
        $this->addSql('DROP INDEX IDX_D34A04AD14959723 ON product');
        $this->addSql('ALTER TABLE product DROP product_type_id');
        $this->addSql('ALTER TABLE product_type ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_type ADD CONSTRAINT FK_13675884584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_13675884584665A ON product_type (product_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200307191411 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, adress LONGTEXT NOT NULL, user_id INT NOT NULL, shipping_cost INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_product (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, p_order_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2530ADE64584665A (product_id), INDEX IDX_2530ADE65095D118 (p_order_id), INDEX IDX_2530ADE6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, sku VARCHAR(20) NOT NULL, cost INT NOT NULL, title VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D34A04ADF9038C4 (sku), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_type (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_13675884584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, name VARCHAR(64) NOT NULL, balance INT NOT NULL, UNIQUE INDEX UNIQ_8D93D6495E237E06 (name), INDEX IDX_8D93D6494584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE65095D118 FOREIGN KEY (p_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_type ADD CONSTRAINT FK_13675884584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE65095D118');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE64584665A');
        $this->addSql('ALTER TABLE product_type DROP FOREIGN KEY FK_13675884584665A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494584665A');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE6A76ED395');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_type');
        $this->addSql('DROP TABLE user');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200522194502 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contactform_tb DROP FOREIGN KEY FK_205882566B3CA4B');
        $this->addSql('DROP INDEX fk_205882566b3ca4b ON contactform_tb');
        $this->addSql('CREATE INDEX contactformtb_userfk ON contactform_tb (id_user)');
        $this->addSql('ALTER TABLE contactform_tb ADD CONSTRAINT FK_205882566B3CA4B FOREIGN KEY (id_user) REFERENCES user_tb (id)');
        $this->addSql('ALTER TABLE order_tb DROP FOREIGN KEY FK_9B90A4116B3CA4B');
        $this->addSql('DROP INDEX fk_9b90a4116b3ca4b ON order_tb');
        $this->addSql('CREATE INDEX ordertb_userfk ON order_tb (id_user)');
        $this->addSql('ALTER TABLE order_tb ADD CONSTRAINT FK_9B90A4116B3CA4B FOREIGN KEY (id_user) REFERENCES user_tb (id)');
        $this->addSql('ALTER TABLE product_feature_tb ADD id_product INT NOT NULL, ADD feature VARCHAR(250) DEFAULT NULL');
        $this->addSql('ALTER TABLE productsonorder_tb DROP FOREIGN KEY FK_B69454FE1BACD2A8');
        $this->addSql('ALTER TABLE productsonorder_tb DROP FOREIGN KEY FK_B69454FEDD7ADDD');
        $this->addSql('DROP INDEX fk_b69454fe1bacd2a8 ON productsonorder_tb');
        $this->addSql('CREATE INDEX pootb_orderfk ON productsonorder_tb (id_order)');
        $this->addSql('DROP INDEX fk_b69454fedd7addd ON productsonorder_tb');
        $this->addSql('CREATE INDEX pootb_productfk ON productsonorder_tb (id_product)');
        $this->addSql('ALTER TABLE productsonorder_tb ADD CONSTRAINT FK_B69454FE1BACD2A8 FOREIGN KEY (id_order) REFERENCES order_tb (id)');
        $this->addSql('ALTER TABLE productsonorder_tb ADD CONSTRAINT FK_B69454FEDD7ADDD FOREIGN KEY (id_product) REFERENCES product_tb (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE contactform_tb DROP FOREIGN KEY FK_205882566B3CA4B');
        $this->addSql('DROP INDEX contactformtb_userfk ON contactform_tb');
        $this->addSql('CREATE INDEX FK_205882566B3CA4B ON contactform_tb (id_user)');
        $this->addSql('ALTER TABLE contactform_tb ADD CONSTRAINT FK_205882566B3CA4B FOREIGN KEY (id_user) REFERENCES user_tb (id)');
        $this->addSql('ALTER TABLE order_tb DROP FOREIGN KEY FK_9B90A4116B3CA4B');
        $this->addSql('DROP INDEX ordertb_userfk ON order_tb');
        $this->addSql('CREATE INDEX FK_9B90A4116B3CA4B ON order_tb (id_user)');
        $this->addSql('ALTER TABLE order_tb ADD CONSTRAINT FK_9B90A4116B3CA4B FOREIGN KEY (id_user) REFERENCES user_tb (id)');
        $this->addSql('ALTER TABLE product_feature_tb DROP id_product, DROP feature');
        $this->addSql('ALTER TABLE productsonorder_tb DROP FOREIGN KEY FK_B69454FE1BACD2A8');
        $this->addSql('ALTER TABLE productsonorder_tb DROP FOREIGN KEY FK_B69454FEDD7ADDD');
        $this->addSql('DROP INDEX pootb_productfk ON productsonorder_tb');
        $this->addSql('CREATE INDEX FK_B69454FEDD7ADDD ON productsonorder_tb (id_product)');
        $this->addSql('DROP INDEX pootb_orderfk ON productsonorder_tb');
        $this->addSql('CREATE INDEX FK_B69454FE1BACD2A8 ON productsonorder_tb (id_order)');
        $this->addSql('ALTER TABLE productsonorder_tb ADD CONSTRAINT FK_B69454FE1BACD2A8 FOREIGN KEY (id_order) REFERENCES order_tb (id)');
        $this->addSql('ALTER TABLE productsonorder_tb ADD CONSTRAINT FK_B69454FEDD7ADDD FOREIGN KEY (id_product) REFERENCES product_tb (id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200511085636 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category_tb DROP FOREIGN KEY id_parent_category');
        $this->addSql('ALTER TABLE category_tb ADD CONSTRAINT FK_44954CB70DF7698 FOREIGN KEY (id_parent_category) REFERENCES category_tb (id)');
        $this->addSql('ALTER TABLE contactform_tb DROP FOREIGN KEY contactformtb_userfk');
        $this->addSql('ALTER TABLE contactform_tb ADD CONSTRAINT FK_205882566B3CA4B FOREIGN KEY (id_user) REFERENCES user_tb (id)');
        $this->addSql('ALTER TABLE order_tb DROP FOREIGN KEY ordertb_userfk');
        $this->addSql('ALTER TABLE order_tb ADD CONSTRAINT FK_9B90A4116B3CA4B FOREIGN KEY (id_user) REFERENCES user_tb (id)');
        $this->addSql('ALTER TABLE productsonorder_tb DROP FOREIGN KEY pootb_orderfk');
        $this->addSql('ALTER TABLE productsonorder_tb DROP FOREIGN KEY pootb_productfk');
        $this->addSql('ALTER TABLE productsonorder_tb CHANGE id_product id_product INT DEFAULT NULL, CHANGE id_order id_order INT DEFAULT NULL');
        $this->addSql('ALTER TABLE productsonorder_tb ADD CONSTRAINT FK_B69454FE1BACD2A8 FOREIGN KEY (id_order) REFERENCES order_tb (id)');
        $this->addSql('ALTER TABLE productsonorder_tb ADD CONSTRAINT FK_B69454FEDD7ADDD FOREIGN KEY (id_product) REFERENCES product_tb (id)');
        $this->addSql('ALTER TABLE product_tb DROP FOREIGN KEY id_category');
        $this->addSql('ALTER TABLE product_tb ADD CONSTRAINT FK_538BAF735697F554 FOREIGN KEY (id_category) REFERENCES category_tb (id)');
        $this->addSql('ALTER TABLE review_tb DROP FOREIGN KEY id_product');
        $this->addSql('ALTER TABLE review_tb DROP FOREIGN KEY id_user');
        $this->addSql('ALTER TABLE review_tb ADD CONSTRAINT FK_28215FB2DD7ADDD FOREIGN KEY (id_product) REFERENCES product_tb (id)');
        $this->addSql('ALTER TABLE review_tb ADD CONSTRAINT FK_28215FB26B3CA4B FOREIGN KEY (id_user) REFERENCES user_tb (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category_tb DROP FOREIGN KEY FK_44954CB70DF7698');
        $this->addSql('ALTER TABLE category_tb ADD CONSTRAINT id_parent_category FOREIGN KEY (id_parent_category) REFERENCES category_tb (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE contactform_tb DROP FOREIGN KEY FK_205882566B3CA4B');
        $this->addSql('ALTER TABLE contactform_tb ADD CONSTRAINT contactformtb_userfk FOREIGN KEY (id_user) REFERENCES user_tb (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE order_tb DROP FOREIGN KEY FK_9B90A4116B3CA4B');
        $this->addSql('ALTER TABLE order_tb ADD CONSTRAINT ordertb_userfk FOREIGN KEY (id_user) REFERENCES user_tb (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE product_tb DROP FOREIGN KEY FK_538BAF735697F554');
        $this->addSql('ALTER TABLE product_tb ADD CONSTRAINT id_category FOREIGN KEY (id_category) REFERENCES category_tb (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE productsonorder_tb DROP FOREIGN KEY FK_B69454FE1BACD2A8');
        $this->addSql('ALTER TABLE productsonorder_tb DROP FOREIGN KEY FK_B69454FEDD7ADDD');
        $this->addSql('ALTER TABLE productsonorder_tb CHANGE id_order id_order INT NOT NULL, CHANGE id_product id_product INT NOT NULL');
        $this->addSql('ALTER TABLE productsonorder_tb ADD CONSTRAINT pootb_orderfk FOREIGN KEY (id_order) REFERENCES order_tb (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE productsonorder_tb ADD CONSTRAINT pootb_productfk FOREIGN KEY (id_product) REFERENCES product_tb (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE review_tb DROP FOREIGN KEY FK_28215FB2DD7ADDD');
        $this->addSql('ALTER TABLE review_tb DROP FOREIGN KEY FK_28215FB26B3CA4B');
        $this->addSql('ALTER TABLE review_tb ADD CONSTRAINT id_product FOREIGN KEY (id_product) REFERENCES product_tb (id) ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE review_tb ADD CONSTRAINT id_user FOREIGN KEY (id_user) REFERENCES user_tb (id) ON UPDATE CASCADE');
    }
}

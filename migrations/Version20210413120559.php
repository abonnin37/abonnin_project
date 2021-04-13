<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210413120559 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1B3750AF4');
        $this->addSql('DROP INDEX IDX_64C19C1B3750AF4 ON category');
        $this->addSql('ALTER TABLE category CHANGE parent_id_id parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_64C19C1727ACA70 ON category (parent_id)');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D9D86650F');
        $this->addSql('DROP INDEX IDX_5A8A6C8D9D86650F ON post');
        $this->addSql('ALTER TABLE post CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('ALTER TABLE post_comment DROP FOREIGN KEY FK_A99CE55FB3750AF4');
        $this->addSql('ALTER TABLE post_comment DROP FOREIGN KEY FK_A99CE55FE85F12B8');
        $this->addSql('DROP INDEX IDX_A99CE55FB3750AF4 ON post_comment');
        $this->addSql('DROP INDEX IDX_A99CE55FE85F12B8 ON post_comment');
        $this->addSql('ALTER TABLE post_comment CHANGE post_id_id post_id INT NOT NULL, CHANGE parent_id_id parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post_comment ADD CONSTRAINT FK_A99CE55F4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_comment ADD CONSTRAINT FK_A99CE55F727ACA70 FOREIGN KEY (parent_id) REFERENCES post_comment (id)');
        $this->addSql('CREATE INDEX IDX_A99CE55F4B89032C ON post_comment (post_id)');
        $this->addSql('CREATE INDEX IDX_A99CE55F727ACA70 ON post_comment (parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('DROP INDEX IDX_64C19C1727ACA70 ON category');
        $this->addSql('ALTER TABLE category CHANGE parent_id parent_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1B3750AF4 FOREIGN KEY (parent_id_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_64C19C1B3750AF4 ON category (parent_id_id)');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('DROP INDEX IDX_5A8A6C8DA76ED395 ON post');
        $this->addSql('ALTER TABLE post CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D9D86650F ON post (user_id_id)');
        $this->addSql('ALTER TABLE post_comment DROP FOREIGN KEY FK_A99CE55F4B89032C');
        $this->addSql('ALTER TABLE post_comment DROP FOREIGN KEY FK_A99CE55F727ACA70');
        $this->addSql('DROP INDEX IDX_A99CE55F4B89032C ON post_comment');
        $this->addSql('DROP INDEX IDX_A99CE55F727ACA70 ON post_comment');
        $this->addSql('ALTER TABLE post_comment CHANGE post_id post_id_id INT NOT NULL, CHANGE parent_id parent_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post_comment ADD CONSTRAINT FK_A99CE55FB3750AF4 FOREIGN KEY (parent_id_id) REFERENCES post_comment (id)');
        $this->addSql('ALTER TABLE post_comment ADD CONSTRAINT FK_A99CE55FE85F12B8 FOREIGN KEY (post_id_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_A99CE55FB3750AF4 ON post_comment (parent_id_id)');
        $this->addSql('CREATE INDEX IDX_A99CE55FE85F12B8 ON post_comment (post_id_id)');
    }
}

<?php

namespace RugbyStatsMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130910163614 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE games ADD field_loc VARCHAR(50) DEFAULT NULL;');
        $this->addSql('ALTER TABLE games ADD field_addr VARCHAR(200) DEFAULT NULL;');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE games DROP field_loc;');
        $this->addSql('ALTER TABLE games DROP field_addr;');
    }
}

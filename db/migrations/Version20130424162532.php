<?php

namespace RugbyStatsMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Alter game roster table to allow null on player ids.
 */
class Version20130424162532 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE game_rosters MODIFY player_ids text DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE game_rosters MODIFY player_ids text DEFAULT NOT NULL');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200930193000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friendship DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE friendship DROP id');
        $this->addSql('ALTER TABLE friendship ADD PRIMARY KEY (user_id, friend_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friendship DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE friendship ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE friendship ADD PRIMARY KEY (id, user_id, friend_id)');
    }
}

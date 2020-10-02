<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201002132113 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CC026A11D');
        $this->addSql('DROP INDEX IDX_9474526CC026A11D ON comment');
        $this->addSql('ALTER TABLE comment DROP comment_child_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD comment_child_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CC026A11D FOREIGN KEY (comment_child_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_9474526CC026A11D ON comment (comment_child_id)');
    }
}

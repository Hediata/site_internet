<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200421092133 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE grades ADD precedent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE361104F6564F9 FOREIGN KEY (precedent_id) REFERENCES grades (id)');
        $this->addSql('CREATE INDEX IDX_3AE361104F6564F9 ON grades (precedent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE361104F6564F9');
        $this->addSql('DROP INDEX IDX_3AE361104F6564F9 ON grades');
        $this->addSql('ALTER TABLE grades DROP precedent_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200411072452 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE commandes (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_35D4282CFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, section_id INT DEFAULT NULL, grade_id INT DEFAULT NULL, login VARCHAR(50) NOT NULL, mot_de_passe VARCHAR(100) NOT NULL, prenom VARCHAR(50) DEFAULT NULL, nom VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_497B315EAA08CB10 (login), INDEX IDX_497B315ED823E37A (section_id), INDEX IDX_497B315EFE19A1A8 (grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grades (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, nom VARCHAR(50) NOT NULL, niveau INT NOT NULL, INDEX IDX_3AE36110D823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande_produit (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, produit_id INT NOT NULL, quantite INT NOT NULL, INDEX IDX_DF1E9E8782EA2E54 (commande_id), INDEX IDX_DF1E9E87F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE types (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_593089306C6E55B5 (nom), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sections (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, nb_membre INT NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produits (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, nom VARCHAR(100) NOT NULL, prix DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(100) DEFAULT NULL, date_creation DATETIME DEFAULT NULL, INDEX IDX_BE2DDF8CC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE utilisateurs ADD CONSTRAINT FK_497B315ED823E37A FOREIGN KEY (section_id) REFERENCES sections (id)');
        $this->addSql('ALTER TABLE utilisateurs ADD CONSTRAINT FK_497B315EFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grades (id)');
        $this->addSql('ALTER TABLE grades ADD CONSTRAINT FK_3AE36110D823E37A FOREIGN KEY (section_id) REFERENCES sections (id)');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E8782EA2E54 FOREIGN KEY (commande_id) REFERENCES commandes (id)');
        $this->addSql('ALTER TABLE commande_produit ADD CONSTRAINT FK_DF1E9E87F347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8CC54C8C93 FOREIGN KEY (type_id) REFERENCES types (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E8782EA2E54');
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282CFB88E14F');
        $this->addSql('ALTER TABLE utilisateurs DROP FOREIGN KEY FK_497B315EFE19A1A8');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8CC54C8C93');
        $this->addSql('ALTER TABLE utilisateurs DROP FOREIGN KEY FK_497B315ED823E37A');
        $this->addSql('ALTER TABLE grades DROP FOREIGN KEY FK_3AE36110D823E37A');
        $this->addSql('ALTER TABLE commande_produit DROP FOREIGN KEY FK_DF1E9E87F347EFB');
        $this->addSql('DROP TABLE commandes');
        $this->addSql('DROP TABLE utilisateurs');
        $this->addSql('DROP TABLE grades');
        $this->addSql('DROP TABLE commande_produit');
        $this->addSql('DROP TABLE types');
        $this->addSql('DROP TABLE sections');
        $this->addSql('DROP TABLE produits');
    }
}

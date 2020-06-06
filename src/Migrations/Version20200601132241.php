<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200601132241 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE infox ADD theme_id INT DEFAULT NULL, ADD visit NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE infox ADD CONSTRAINT FK_79185BE559027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('CREATE INDEX IDX_79185BE559027487 ON infox (theme_id)');
        $this->addSql('ALTER TABLE personne ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personne ADD CONSTRAINT FK_FCEC9EFBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_FCEC9EFBCF5E72D ON personne (categorie_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE infox DROP FOREIGN KEY FK_79185BE559027487');
        $this->addSql('DROP INDEX IDX_79185BE559027487 ON infox');
        $this->addSql('ALTER TABLE infox DROP theme_id, DROP visit');
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EFBCF5E72D');
        $this->addSql('DROP INDEX IDX_FCEC9EFBCF5E72D ON personne');
        $this->addSql('ALTER TABLE personne DROP categorie_id');
    }
}

<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180119140822 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C60663379586');
        $this->addSql('DROP INDEX IDX_50D0C60663379586 ON answers');
        $this->addSql('ALTER TABLE answers DROP comments_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answers ADD comments_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C60663379586 FOREIGN KEY (comments_id) REFERENCES comments (id)');
        $this->addSql('CREATE INDEX IDX_50D0C60663379586 ON answers (comments_id)');
    }
}

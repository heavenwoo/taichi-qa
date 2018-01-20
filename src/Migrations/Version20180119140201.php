<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180119140201 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, post_id INT DEFAULT NULL, user_id INT DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5F9E962A1E27F6BF (question_id), INDEX IDX_5F9E962A4B89032C (post_id), INDEX IDX_5F9E962AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, subject LONGTEXT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_tags (post_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_A6E9F32D4B89032C (post_id), INDEX IDX_A6E9F32DBAD26311 (tag_id), PRIMARY KEY(post_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A4B89032C FOREIGN KEY (post_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE post_tags ADD CONSTRAINT FK_A6E9F32D4B89032C FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tags ADD CONSTRAINT FK_A6E9F32DBAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE answers ADD comments_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C60663379586 FOREIGN KEY (comments_id) REFERENCES comments (id)');
        $this->addSql('CREATE INDEX IDX_50D0C60663379586 ON answers (comments_id)');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D512469DE2');
        $this->addSql('DROP INDEX IDX_8ADC54D512469DE2 ON questions');
        $this->addSql('ALTER TABLE questions DROP category_id');
        $this->addSql('ALTER TABLE tags ADD description LONGTEXT NOT NULL, CHANGE name name VARCHAR(15) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C60663379586');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A4B89032C');
        $this->addSql('ALTER TABLE post_tags DROP FOREIGN KEY FK_A6E9F32D4B89032C');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE post_tags');
        $this->addSql('DROP INDEX IDX_50D0C60663379586 ON answers');
        $this->addSql('ALTER TABLE answers DROP comments_id');
        $this->addSql('ALTER TABLE questions ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D512469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_8ADC54D512469DE2 ON questions (category_id)');
        $this->addSql('ALTER TABLE tags DROP description, CHANGE name name TINYTEXT NOT NULL COLLATE utf8_unicode_ci');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219125340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agent (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, login_time DATETIME DEFAULT NULL, role VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_268B9C9DF85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE asset (id INT AUTO_INCREMENT NOT NULL, bid NUMERIC(10, 2) NOT NULL, ask NUMERIC(10, 2) NOT NULL, date_update DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, agent_id INT DEFAULT NULL, action_name VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_8F3F68C5A76ED395 (user_id), INDEX IDX_8F3F68C53414710B (agent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trade (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, agent_id INT DEFAULT NULL, trade_size NUMERIC(10, 2) NOT NULL, lot_count NUMERIC(10, 2) NOT NULL, pnl NUMERIC(10, 2) NOT NULL, payout NUMERIC(10, 2) DEFAULT NULL, used_margin NUMERIC(10, 2) NOT NULL, entry_rate NUMERIC(10, 2) NOT NULL, close_rate NUMERIC(10, 2) DEFAULT NULL, date_created DATETIME NOT NULL, date_close DATETIME DEFAULT NULL, status VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, INDEX IDX_7E1A4366A76ED395 (user_id), INDEX IDX_7E1A43663414710B (agent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, login_time DATETIME DEFAULT NULL, currency VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C53414710B FOREIGN KEY (agent_id) REFERENCES agent (id)');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A4366A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A43663414710B FOREIGN KEY (agent_id) REFERENCES agent (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C5A76ED395');
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C53414710B');
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A4366A76ED395');
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A43663414710B');
        $this->addSql('DROP TABLE agent');
        $this->addSql('DROP TABLE asset');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE trade');
        $this->addSql('DROP TABLE user');
    }
}

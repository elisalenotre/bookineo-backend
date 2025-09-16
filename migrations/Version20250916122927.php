<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916122927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rentals (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, renter_first_name VARCHAR(255) NOT NULL, renter_last_name VARCHAR(255) NOT NULL, start_date DATE NOT NULL, due_date DATE NOT NULL, return_date DATE DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_35ACDB4816A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB4816A2B381 FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB4816A2B381');
        $this->addSql('DROP TABLE rentals');
    }
}

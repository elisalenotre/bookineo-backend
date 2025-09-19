<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250919092436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rentals DROP renter_first_name, DROP renter_last_name, CHANGE start_date start_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE due_date due_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', CHANGE return_date return_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE renter_email renter_email VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rentals ADD renter_first_name VARCHAR(255) NOT NULL, ADD renter_last_name VARCHAR(255) NOT NULL, CHANGE renter_email renter_email VARCHAR(180) DEFAULT NULL, CHANGE start_date start_date DATE NOT NULL, CHANGE due_date due_date DATE NOT NULL, CHANGE return_date return_date DATE DEFAULT NULL');
    }
}

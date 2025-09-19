<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250919140801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            UPDATE books 
            SET renter_email = LEFT(renter_email, 320) 
            WHERE renter_email IS NOT NULL 
            AND CHAR_LENGTH(renter_email) > 320
        ");

        $this->addSql("
            ALTER TABLE books 
            MODIFY renter_email VARCHAR(320) DEFAULT NULL
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            ALTER TABLE books 
            MODIFY renter_email VARCHAR(255) DEFAULT NULL
        ");
    }

}

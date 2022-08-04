<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220804113737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, room_id INTEGER NOT NULL, entry_date DATE NOT NULL, exit_date DATE NOT NULL, guest_number INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL, locator VARCHAR(255) NOT NULL, contact_details CLOB NOT NULL --(DC2Type:json)
        )');
        $this->addSql('CREATE INDEX IDX_42C8495554177093 ON reservation (room_id)');
        $this->addSql('CREATE TABLE room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type_room_id INTEGER NOT NULL, number INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_729F519B7C361F66 ON room (type_room_id)');
        $this->addSql('CREATE TABLE type_room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price_day DOUBLE PRECISION NOT NULL, number_of_rooms INTEGER NOT NULL, max_guests INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE type_room');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

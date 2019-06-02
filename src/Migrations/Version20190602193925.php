<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190602193925 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE currency_rating_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE country_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE currency_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE currency_rating (id INT NOT NULL, currency_id INT NOT NULL, rating_date DATE NOT NULL, rate DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7C0F004D38248176 ON currency_rating (currency_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_currency_rate ON currency_rating (rating_date, currency_id)');
        $this->addSql('COMMENT ON COLUMN currency_rating.rating_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE country (id INT NOT NULL, country_name VARCHAR(255) NOT NULL, country_native_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5373C966D910F5E2 ON country (country_name)');
        $this->addSql('CREATE TABLE country_currency (country_id INT NOT NULL, currency_id INT NOT NULL, PRIMARY KEY(country_id, currency_id))');
        $this->addSql('CREATE INDEX IDX_5A9CD982F92F3E70 ON country_currency (country_id)');
        $this->addSql('CREATE INDEX IDX_5A9CD98238248176 ON country_currency (currency_id)');
        $this->addSql('CREATE TABLE currency (id INT NOT NULL, currency_name VARCHAR(150) NOT NULL, currency_code VARCHAR(3) NOT NULL, currency_symbol VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6956883FD4943D72 ON currency (currency_name)');
        $this->addSql('ALTER TABLE currency_rating ADD CONSTRAINT FK_7C0F004D38248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE country_currency ADD CONSTRAINT FK_5A9CD982F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE country_currency ADD CONSTRAINT FK_5A9CD98238248176 FOREIGN KEY (currency_id) REFERENCES currency (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE country_currency DROP CONSTRAINT FK_5A9CD982F92F3E70');
        $this->addSql('ALTER TABLE currency_rating DROP CONSTRAINT FK_7C0F004D38248176');
        $this->addSql('ALTER TABLE country_currency DROP CONSTRAINT FK_5A9CD98238248176');
        $this->addSql('DROP SEQUENCE currency_rating_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE country_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE currency_id_seq CASCADE');
        $this->addSql('DROP TABLE currency_rating');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE country_currency');
        $this->addSql('DROP TABLE currency');
    }
}

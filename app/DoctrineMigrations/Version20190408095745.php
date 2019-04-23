<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190408095745 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE city_probe_point (id INT NOT NULL, asset_id INT DEFAULT NULL, noise LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', co INT NOT NULL, temperature DOUBLE PRECISION NOT NULL, pm10 INT NOT NULL, battery DOUBLE PRECISION NOT NULL, rain LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', humidity DOUBLE PRECISION NOT NULL, illuminance INT NOT NULL, atmosphericPressure DOUBLE PRECISION NOT NULL, pm25 INT NOT NULL, no2 INT NOT NULL, firmwareVersion INT DEFAULT NULL, time_instant DATETIME NOT NULL, INDEX IDX_689C5F5DA1941 (asset_id), INDEX search_idx (time_instant), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city_probe_point ADD CONSTRAINT FK_689C5F5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2AF5A5C772E836A ON asset (identifier)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE city_probe_point');
        $this->addSql('DROP INDEX UNIQ_2AF5A5C772E836A ON asset');
    }
}

<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180727001914 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE asset (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city_lab_point (id INT AUTO_INCREMENT NOT NULL, asset_id INT DEFAULT NULL, atmospheric_pressure DOUBLE PRECISION DEFAULT NULL, battery_level DOUBLE PRECISION DEFAULT NULL, charging_power INT DEFAULT NULL, height_above_mean_sea_level SMALLINT DEFAULT NULL, outside_humidity DOUBLE PRECISION DEFAULT NULL, outside_temperature DOUBLE PRECISION DEFAULT NULL, water_temperature DOUBLE PRECISION DEFAULT NULL, daylight INT DEFAULT NULL, rainfall INT DEFAULT NULL, sunlight_par DOUBLE PRECISION DEFAULT NULL, wind_direction VARCHAR(3) DEFAULT NULL, wind_speed INT DEFAULT NULL, time_instant DATETIME NOT NULL, INDEX IDX_743D39F95DA1941 (asset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dokk1book_returns_point (id INT AUTO_INCREMENT NOT NULL, asset_id INT DEFAULT NULL, returns_past24hours INT NOT NULL, returns_past5min INT NOT NULL, returns_past60min INT NOT NULL, returns_today INT NOT NULL, time_instant DATETIME NOT NULL, INDEX IDX_5AA05D6A5DA1941 (asset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dokk1counters_point (id INT AUTO_INCREMENT NOT NULL, asset_id INT DEFAULT NULL, visitors_in INT NOT NULL, visitors_out INT NOT NULL, time_instant DATETIME NOT NULL, INDEX IDX_A87940945DA1941 (asset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE real_time_parking_point (id INT AUTO_INCREMENT NOT NULL, asset_id INT DEFAULT NULL, extra_spot_number INT NOT NULL, total_spot_number INT NOT NULL, time_instant DATETIME NOT NULL, INDEX IDX_1C3C9F305DA1941 (asset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE real_time_solar_array_point (id INT AUTO_INCREMENT NOT NULL, asset_id INT DEFAULT NULL, current_production INT NOT NULL, daily_max_production INT NOT NULL, daily_production INT NOT NULL, total_production INT NOT NULL, time_instant DATETIME NOT NULL, INDEX IDX_28E91C3B5DA1941 (asset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE real_time_traffic_point (id INT AUTO_INCREMENT NOT NULL, asset_id INT DEFAULT NULL, speed_average INT NOT NULL, time_instant DATETIME NOT NULL, INDEX IDX_44DA010B5DA1941 (asset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city_lab_point ADD CONSTRAINT FK_743D39F95DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE dokk1book_returns_point ADD CONSTRAINT FK_5AA05D6A5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE dokk1counters_point ADD CONSTRAINT FK_A87940945DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE real_time_parking_point ADD CONSTRAINT FK_1C3C9F305DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE real_time_solar_array_point ADD CONSTRAINT FK_28E91C3B5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE real_time_traffic_point ADD CONSTRAINT FK_44DA010B5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city_lab_point DROP FOREIGN KEY FK_743D39F95DA1941');
        $this->addSql('ALTER TABLE dokk1book_returns_point DROP FOREIGN KEY FK_5AA05D6A5DA1941');
        $this->addSql('ALTER TABLE dokk1counters_point DROP FOREIGN KEY FK_A87940945DA1941');
        $this->addSql('ALTER TABLE real_time_parking_point DROP FOREIGN KEY FK_1C3C9F305DA1941');
        $this->addSql('ALTER TABLE real_time_solar_array_point DROP FOREIGN KEY FK_28E91C3B5DA1941');
        $this->addSql('ALTER TABLE real_time_traffic_point DROP FOREIGN KEY FK_44DA010B5DA1941');
        $this->addSql('DROP TABLE asset');
        $this->addSql('DROP TABLE city_lab_point');
        $this->addSql('DROP TABLE dokk1book_returns_point');
        $this->addSql('DROP TABLE dokk1counters_point');
        $this->addSql('DROP TABLE real_time_parking_point');
        $this->addSql('DROP TABLE real_time_solar_array_point');
        $this->addSql('DROP TABLE real_time_traffic_point');
    }
}

<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180801093154 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX search_idx ON city_lab_point (time_instant)');
        $this->addSql('CREATE INDEX search_idx ON dokk1book_returns_point (time_instant)');
        $this->addSql('CREATE INDEX search_idx ON dokk1counters_point (time_instant)');
        $this->addSql('CREATE INDEX search_idx ON real_time_parking_point (time_instant)');
        $this->addSql('CREATE INDEX search_idx ON real_time_solar_array_point (time_instant)');
        $this->addSql('CREATE INDEX search_idx ON real_time_traffic_point (time_instant)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX search_idx ON city_lab_point');
        $this->addSql('DROP INDEX search_idx ON dokk1book_returns_point');
        $this->addSql('DROP INDEX search_idx ON dokk1counters_point');
        $this->addSql('DROP INDEX search_idx ON real_time_parking_point');
        $this->addSql('DROP INDEX search_idx ON real_time_solar_array_point');
        $this->addSql('DROP INDEX search_idx ON real_time_traffic_point');
    }
}

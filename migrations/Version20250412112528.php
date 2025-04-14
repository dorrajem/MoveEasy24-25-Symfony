<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250412112528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE train');
        $this->addSql('ALTER TABLE itineraire DROP train');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY station_ibfk_1');
        $this->addSql('DROP INDEX idItineraire ON station');
        $this->addSql('ALTER TABLE station ADD itineraire_id INT NOT NULL, ADD nom_station VARCHAR(255) NOT NULL, DROP nomStation, DROP idItineraire');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B1A9B853B8 FOREIGN KEY (itineraire_id) REFERENCES itineraire (idItineraire)');
        $this->addSql('CREATE INDEX IDX_9F39F8B1A9B853B8 ON station (itineraire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE train (idTrain INT AUTO_INCREMENT NOT NULL, typeTrain VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, capacite INT NOT NULL, statut VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'En service\' COLLATE `utf8mb4_general_ci`, dateMiseEnService DATE NOT NULL, PRIMARY KEY(idTrain)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE itineraire ADD train INT NOT NULL');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B1A9B853B8');
        $this->addSql('DROP INDEX IDX_9F39F8B1A9B853B8 ON station');
        $this->addSql('ALTER TABLE station ADD nomStation VARCHAR(100) NOT NULL, ADD idItineraire INT DEFAULT NULL, DROP itineraire_id, DROP nom_station');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT station_ibfk_1 FOREIGN KEY (idItineraire) REFERENCES itineraire (idItineraire) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX idItineraire ON station (idItineraire)');
    }
}

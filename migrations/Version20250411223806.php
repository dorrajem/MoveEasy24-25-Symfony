<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411223806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE maintenance_eq (id INT AUTO_INCREMENT NOT NULL, id_maintenance INT NOT NULL, id_equipement INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, période VARCHAR(255) DEFAULT NULL, date_maintenance DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE maintenanceequipement DROP FOREIGN KEY maintenanceequipement_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE maintenanceequipement
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE maintenanceequipement (idMaintenance INT AUTO_INCREMENT NOT NULL, idEquipement INT DEFAULT NULL, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, période VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, dateMaintenance DATE DEFAULT NULL, INDEX idEquipement (idEquipement), PRIMARY KEY(idMaintenance)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE maintenanceequipement ADD CONSTRAINT maintenanceequipement_ibfk_1 FOREIGN KEY (idEquipement) REFERENCES equipement (idEquipement)
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE maintenance_eq
        SQL);
    }
}

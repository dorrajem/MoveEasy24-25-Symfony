<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250409154905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE equipement CHANGE idEquipement idEquipement INT AUTO_INCREMENT NOT NULL, CHANGE typeEquipement type_equipement VARCHAR(255) DEFAULT NULL, CHANGE quantiteDisponible quantite_disponible INT DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE equipement CHANGE idEquipement idEquipement INT NOT NULL, CHANGE type_equipement typeEquipement VARCHAR(255) DEFAULT NULL, CHANGE quantite_disponible quantiteDisponible INT DEFAULT NULL
        SQL);
    }
}

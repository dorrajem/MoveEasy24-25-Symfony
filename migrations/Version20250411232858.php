<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411232858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE maintenance_eq CHANGE id_equipement idEquipement INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE maintenance_eq ADD CONSTRAINT FK_37AAA95B3A40D819 FOREIGN KEY (idEquipement) REFERENCES equipement (idEquipement)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_37AAA95B3A40D819 ON maintenance_eq (idEquipement)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE maintenance_eq DROP FOREIGN KEY FK_37AAA95B3A40D819
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_37AAA95B3A40D819 ON maintenance_eq
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE maintenance_eq CHANGE idEquipement id_equipement INT DEFAULT NULL
        SQL);
    }
}

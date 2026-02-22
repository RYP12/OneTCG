<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

// SECCIÓN: Migración — Añadir puntuación a item_ranking
final class Version20260222130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Añade el campo puntuacion a item_ranking para permitir valorar cada posición del ranking';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE onetcg.item_ranking ADD COLUMN puntuacion INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE onetcg.item_ranking DROP COLUMN puntuacion');
    }
}

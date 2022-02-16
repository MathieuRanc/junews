<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210621130738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_FDCA8C9C6AB213CC');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cours AS SELECT id, lieu_id, nom, date_debut, date_fin, description, examen FROM cours');
        $this->addSql('DROP TABLE cours');
        $this->addSql('CREATE TABLE cours (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, lieu_id INTEGER DEFAULT NULL, nom VARCHAR(255) NOT NULL COLLATE BINARY, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, description CLOB NOT NULL COLLATE BINARY, examen BOOLEAN DEFAULT NULL, CONSTRAINT FK_FDCA8C9C6AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cours (id, lieu_id, nom, date_debut, date_fin, description, examen) SELECT id, lieu_id, nom, date_debut, date_fin, description, examen FROM __temp__cours');
        $this->addSql('DROP TABLE __temp__cours');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C6AB213CC ON cours (lieu_id)');
        $this->addSql('DROP INDEX IDX_717E22E3139DF194');
        $this->addSql('DROP INDEX UNIQ_717E22E3E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__etudiant AS SELECT id, promotion_id, email, roles, password, nom, prenom, administre, photo FROM etudiant');
        $this->addSql('DROP TABLE etudiant');
        $this->addSql('CREATE TABLE etudiant (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, promotion_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , password VARCHAR(255) NOT NULL COLLATE BINARY, nom VARCHAR(255) NOT NULL COLLATE BINARY, prenom VARCHAR(255) NOT NULL COLLATE BINARY, administre INTEGER DEFAULT NULL, photo INTEGER DEFAULT NULL, CONSTRAINT FK_717E22E3139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO etudiant (id, promotion_id, email, roles, password, nom, prenom, administre, photo) SELECT id, promotion_id, email, roles, password, nom, prenom, administre, photo FROM __temp__etudiant');
        $this->addSql('DROP TABLE __temp__etudiant');
        $this->addSql('CREATE INDEX IDX_717E22E3139DF194 ON etudiant (promotion_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_717E22E3E7927C74 ON etudiant (email)');
        $this->addSql('DROP INDEX IDX_C5AD8D69FD02F13');
        $this->addSql('DROP INDEX IDX_C5AD8D69DDEAB1A3');
        $this->addSql('CREATE TEMPORARY TABLE __temp__etudiant_evenement AS SELECT etudiant_id, evenement_id FROM etudiant_evenement');
        $this->addSql('DROP TABLE etudiant_evenement');
        $this->addSql('CREATE TABLE etudiant_evenement (etudiant_id INTEGER NOT NULL, evenement_id INTEGER NOT NULL, PRIMARY KEY(etudiant_id, evenement_id), CONSTRAINT FK_C5AD8D69DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C5AD8D69FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO etudiant_evenement (etudiant_id, evenement_id) SELECT etudiant_id, evenement_id FROM __temp__etudiant_evenement');
        $this->addSql('DROP TABLE __temp__etudiant_evenement');
        $this->addSql('CREATE INDEX IDX_C5AD8D69FD02F13 ON etudiant_evenement (evenement_id)');
        $this->addSql('CREATE INDEX IDX_C5AD8D69DDEAB1A3 ON etudiant_evenement (etudiant_id)');
        $this->addSql('DROP INDEX IDX_4D9B1681EFB9C8A5');
        $this->addSql('DROP INDEX IDX_4D9B1681DDEAB1A3');
        $this->addSql('CREATE TEMPORARY TABLE __temp__etudiant_association AS SELECT etudiant_id, association_id FROM etudiant_association');
        $this->addSql('DROP TABLE etudiant_association');
        $this->addSql('CREATE TABLE etudiant_association (etudiant_id INTEGER NOT NULL, association_id INTEGER NOT NULL, PRIMARY KEY(etudiant_id, association_id), CONSTRAINT FK_4D9B1681DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4D9B1681EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO etudiant_association (etudiant_id, association_id) SELECT etudiant_id, association_id FROM __temp__etudiant_association');
        $this->addSql('DROP TABLE __temp__etudiant_association');
        $this->addSql('CREATE INDEX IDX_4D9B1681EFB9C8A5 ON etudiant_association (association_id)');
        $this->addSql('CREATE INDEX IDX_4D9B1681DDEAB1A3 ON etudiant_association (etudiant_id)');
        $this->addSql('DROP INDEX IDX_82F0A0807ECF78B0');
        $this->addSql('DROP INDEX IDX_82F0A080DDEAB1A3');
        $this->addSql('CREATE TEMPORARY TABLE __temp__etudiant_cours AS SELECT etudiant_id, cours_id FROM etudiant_cours');
        $this->addSql('DROP TABLE etudiant_cours');
        $this->addSql('CREATE TABLE etudiant_cours (etudiant_id INTEGER NOT NULL, cours_id INTEGER NOT NULL, PRIMARY KEY(etudiant_id, cours_id), CONSTRAINT FK_82F0A080DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_82F0A0807ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO etudiant_cours (etudiant_id, cours_id) SELECT etudiant_id, cours_id FROM __temp__etudiant_cours');
        $this->addSql('DROP TABLE __temp__etudiant_cours');
        $this->addSql('CREATE INDEX IDX_82F0A0807ECF78B0 ON etudiant_cours (cours_id)');
        $this->addSql('CREATE INDEX IDX_82F0A080DDEAB1A3 ON etudiant_cours (etudiant_id)');
        $this->addSql('DROP INDEX IDX_B26681EEFB9C8A5');
        $this->addSql('DROP INDEX IDX_B26681E6AB213CC');
        $this->addSql('CREATE TEMPORARY TABLE __temp__evenement AS SELECT id, lieu_id, association_id, nom, description, date_debut, date_fin, image FROM evenement');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('CREATE TABLE evenement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, lieu_id INTEGER DEFAULT NULL, association_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL COLLATE BINARY, description VARCHAR(255) NOT NULL COLLATE BINARY, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, image INTEGER DEFAULT NULL, CONSTRAINT FK_B26681E6AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B26681EEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO evenement (id, lieu_id, association_id, nom, description, date_debut, date_fin, image) SELECT id, lieu_id, association_id, nom, description, date_debut, date_fin, image FROM __temp__evenement');
        $this->addSql('DROP TABLE __temp__evenement');
        $this->addSql('CREATE INDEX IDX_B26681EEFB9C8A5 ON evenement (association_id)');
        $this->addSql('CREATE INDEX IDX_B26681E6AB213CC ON evenement (lieu_id)');
        $this->addSql('DROP INDEX IDX_97B42B7916880AAF');
        $this->addSql('DROP INDEX IDX_97B42B79FD02F13');
        $this->addSql('CREATE TEMPORARY TABLE __temp__evenement_materiel AS SELECT evenement_id, materiel_id FROM evenement_materiel');
        $this->addSql('DROP TABLE evenement_materiel');
        $this->addSql('CREATE TABLE evenement_materiel (evenement_id INTEGER NOT NULL, materiel_id INTEGER NOT NULL, PRIMARY KEY(evenement_id, materiel_id), CONSTRAINT FK_97B42B79FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_97B42B7916880AAF FOREIGN KEY (materiel_id) REFERENCES materiel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO evenement_materiel (evenement_id, materiel_id) SELECT evenement_id, materiel_id FROM __temp__evenement_materiel');
        $this->addSql('DROP TABLE __temp__evenement_materiel');
        $this->addSql('CREATE INDEX IDX_97B42B7916880AAF ON evenement_materiel (materiel_id)');
        $this->addSql('CREATE INDEX IDX_97B42B79FD02F13 ON evenement_materiel (evenement_id)');
        $this->addSql('DROP INDEX IDX_6F437100139DF194');
        $this->addSql('DROP INDEX IDX_6F437100FD02F13');
        $this->addSql('CREATE TEMPORARY TABLE __temp__evenement_promotion AS SELECT evenement_id, promotion_id FROM evenement_promotion');
        $this->addSql('DROP TABLE evenement_promotion');
        $this->addSql('CREATE TABLE evenement_promotion (evenement_id INTEGER NOT NULL, promotion_id INTEGER NOT NULL, PRIMARY KEY(evenement_id, promotion_id), CONSTRAINT FK_6F437100FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6F437100139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO evenement_promotion (evenement_id, promotion_id) SELECT evenement_id, promotion_id FROM __temp__evenement_promotion');
        $this->addSql('DROP TABLE __temp__evenement_promotion');
        $this->addSql('CREATE INDEX IDX_6F437100139DF194 ON evenement_promotion (promotion_id)');
        $this->addSql('CREATE INDEX IDX_6F437100FD02F13 ON evenement_promotion (evenement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_FDCA8C9C6AB213CC');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cours AS SELECT id, lieu_id, nom, date_debut, date_fin, description, examen FROM cours');
        $this->addSql('DROP TABLE cours');
        $this->addSql('CREATE TABLE cours (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, lieu_id INTEGER DEFAULT NULL, nom VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, description CLOB NOT NULL, examen BOOLEAN DEFAULT NULL)');
        $this->addSql('INSERT INTO cours (id, lieu_id, nom, date_debut, date_fin, description, examen) SELECT id, lieu_id, nom, date_debut, date_fin, description, examen FROM __temp__cours');
        $this->addSql('DROP TABLE __temp__cours');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C6AB213CC ON cours (lieu_id)');
        $this->addSql('DROP INDEX UNIQ_717E22E3E7927C74');
        $this->addSql('DROP INDEX IDX_717E22E3139DF194');
        $this->addSql('CREATE TEMPORARY TABLE __temp__etudiant AS SELECT id, promotion_id, email, roles, password, nom, prenom, administre, photo FROM etudiant');
        $this->addSql('DROP TABLE etudiant');
        $this->addSql('CREATE TABLE etudiant (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, promotion_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, administre INTEGER DEFAULT NULL, photo INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO etudiant (id, promotion_id, email, roles, password, nom, prenom, administre, photo) SELECT id, promotion_id, email, roles, password, nom, prenom, administre, photo FROM __temp__etudiant');
        $this->addSql('DROP TABLE __temp__etudiant');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_717E22E3E7927C74 ON etudiant (email)');
        $this->addSql('CREATE INDEX IDX_717E22E3139DF194 ON etudiant (promotion_id)');
        $this->addSql('DROP INDEX IDX_4D9B1681DDEAB1A3');
        $this->addSql('DROP INDEX IDX_4D9B1681EFB9C8A5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__etudiant_association AS SELECT etudiant_id, association_id FROM etudiant_association');
        $this->addSql('DROP TABLE etudiant_association');
        $this->addSql('CREATE TABLE etudiant_association (etudiant_id INTEGER NOT NULL, association_id INTEGER NOT NULL, PRIMARY KEY(etudiant_id, association_id))');
        $this->addSql('INSERT INTO etudiant_association (etudiant_id, association_id) SELECT etudiant_id, association_id FROM __temp__etudiant_association');
        $this->addSql('DROP TABLE __temp__etudiant_association');
        $this->addSql('CREATE INDEX IDX_4D9B1681DDEAB1A3 ON etudiant_association (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_4D9B1681EFB9C8A5 ON etudiant_association (association_id)');
        $this->addSql('DROP INDEX IDX_82F0A080DDEAB1A3');
        $this->addSql('DROP INDEX IDX_82F0A0807ECF78B0');
        $this->addSql('CREATE TEMPORARY TABLE __temp__etudiant_cours AS SELECT etudiant_id, cours_id FROM etudiant_cours');
        $this->addSql('DROP TABLE etudiant_cours');
        $this->addSql('CREATE TABLE etudiant_cours (etudiant_id INTEGER NOT NULL, cours_id INTEGER NOT NULL, PRIMARY KEY(etudiant_id, cours_id))');
        $this->addSql('INSERT INTO etudiant_cours (etudiant_id, cours_id) SELECT etudiant_id, cours_id FROM __temp__etudiant_cours');
        $this->addSql('DROP TABLE __temp__etudiant_cours');
        $this->addSql('CREATE INDEX IDX_82F0A080DDEAB1A3 ON etudiant_cours (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_82F0A0807ECF78B0 ON etudiant_cours (cours_id)');
        $this->addSql('DROP INDEX IDX_C5AD8D69DDEAB1A3');
        $this->addSql('DROP INDEX IDX_C5AD8D69FD02F13');
        $this->addSql('CREATE TEMPORARY TABLE __temp__etudiant_evenement AS SELECT etudiant_id, evenement_id FROM etudiant_evenement');
        $this->addSql('DROP TABLE etudiant_evenement');
        $this->addSql('CREATE TABLE etudiant_evenement (etudiant_id INTEGER NOT NULL, evenement_id INTEGER NOT NULL, PRIMARY KEY(etudiant_id, evenement_id))');
        $this->addSql('INSERT INTO etudiant_evenement (etudiant_id, evenement_id) SELECT etudiant_id, evenement_id FROM __temp__etudiant_evenement');
        $this->addSql('DROP TABLE __temp__etudiant_evenement');
        $this->addSql('CREATE INDEX IDX_C5AD8D69DDEAB1A3 ON etudiant_evenement (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_C5AD8D69FD02F13 ON etudiant_evenement (evenement_id)');
        $this->addSql('DROP INDEX IDX_B26681E6AB213CC');
        $this->addSql('DROP INDEX IDX_B26681EEFB9C8A5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__evenement AS SELECT id, lieu_id, association_id, nom, description, date_debut, date_fin, image FROM evenement');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('CREATE TABLE evenement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, lieu_id INTEGER DEFAULT NULL, association_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, image INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO evenement (id, lieu_id, association_id, nom, description, date_debut, date_fin, image) SELECT id, lieu_id, association_id, nom, description, date_debut, date_fin, image FROM __temp__evenement');
        $this->addSql('DROP TABLE __temp__evenement');
        $this->addSql('CREATE INDEX IDX_B26681E6AB213CC ON evenement (lieu_id)');
        $this->addSql('CREATE INDEX IDX_B26681EEFB9C8A5 ON evenement (association_id)');
        $this->addSql('DROP INDEX IDX_97B42B79FD02F13');
        $this->addSql('DROP INDEX IDX_97B42B7916880AAF');
        $this->addSql('CREATE TEMPORARY TABLE __temp__evenement_materiel AS SELECT evenement_id, materiel_id FROM evenement_materiel');
        $this->addSql('DROP TABLE evenement_materiel');
        $this->addSql('CREATE TABLE evenement_materiel (evenement_id INTEGER NOT NULL, materiel_id INTEGER NOT NULL, PRIMARY KEY(evenement_id, materiel_id))');
        $this->addSql('INSERT INTO evenement_materiel (evenement_id, materiel_id) SELECT evenement_id, materiel_id FROM __temp__evenement_materiel');
        $this->addSql('DROP TABLE __temp__evenement_materiel');
        $this->addSql('CREATE INDEX IDX_97B42B79FD02F13 ON evenement_materiel (evenement_id)');
        $this->addSql('CREATE INDEX IDX_97B42B7916880AAF ON evenement_materiel (materiel_id)');
        $this->addSql('DROP INDEX IDX_6F437100FD02F13');
        $this->addSql('DROP INDEX IDX_6F437100139DF194');
        $this->addSql('CREATE TEMPORARY TABLE __temp__evenement_promotion AS SELECT evenement_id, promotion_id FROM evenement_promotion');
        $this->addSql('DROP TABLE evenement_promotion');
        $this->addSql('CREATE TABLE evenement_promotion (evenement_id INTEGER NOT NULL, promotion_id INTEGER NOT NULL, PRIMARY KEY(evenement_id, promotion_id))');
        $this->addSql('INSERT INTO evenement_promotion (evenement_id, promotion_id) SELECT evenement_id, promotion_id FROM __temp__evenement_promotion');
        $this->addSql('DROP TABLE __temp__evenement_promotion');
        $this->addSql('CREATE INDEX IDX_6F437100FD02F13 ON evenement_promotion (evenement_id)');
        $this->addSql('CREATE INDEX IDX_6F437100139DF194 ON evenement_promotion (promotion_id)');
    }
}

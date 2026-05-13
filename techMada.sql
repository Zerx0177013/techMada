PRAGMA foreign_keys = OFF;

DROP TABLE IF EXISTS departements;
CREATE TABLE departements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    description TEXT
);

DROP TABLE IF EXISTS typesConge;
CREATE TABLE typesConge (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL,
    joursAnnuels INTEGER NOT NULL,
    deductible INTEGER NOT NULL CHECK (deductible IN (0, 1)) DEFAULT 1
);

DROP TABLE IF EXISTS employes;
CREATE TABLE employes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    prenom TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK (role IN ('employe', 'rh', 'admin')),
    DepartementId INTEGER,
    dateEmbauche TEXT NOT NULL,
    actif INTEGER NOT NULL CHECK (actif IN (0, 1)) DEFAULT 1,
    FOREIGN KEY (DepartementId) REFERENCES departements(id) ON DELETE SET NULL
);

DROP TABLE IF EXISTS soldes;
CREATE TABLE soldes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    EmployeId INTEGER NOT NULL,
    TypeCongeId INTEGER NOT NULL,
    annee INTEGER NOT NULL,
    joursAttribues INTEGER NOT NULL,
    joursPris INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY (EmployeId) REFERENCES employes(id) ON DELETE CASCADE,
    FOREIGN KEY (TypeCongeId) REFERENCES typesConge(id) ON DELETE CASCADE,
    UNIQUE (EmployeId, TypeCongeId, annee)
);

DROP TABLE IF EXISTS conges;
CREATE TABLE conges (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    EmployeId INTEGER NOT NULL,
    TypeCongeId INTEGER NOT NULL,
    dateDebut TEXT NOT NULL,
    dateFin TEXT NOT NULL,
    nbJours INTEGER NOT NULL,
    motif TEXT,
    statut TEXT NOT NULL CHECK (statut IN ('enAttente', 'approuvee', 'refusee', 'annulee')) DEFAULT 'enAttente',
    commentaireRh TEXT,
    createdAt TEXT DEFAULT (datetime('now', 'localtime')),
    TraitePar INTEGER,
    FOREIGN KEY (EmployeId) REFERENCES employes(id) ON DELETE CASCADE,
    FOREIGN KEY (TypeCongeId) REFERENCES typesConge(id) ON DELETE RESTRICT,
    FOREIGN KEY (TraitePar) REFERENCES employes(id) ON DELETE SET NULL
);

PRAGMA foreign_keys = ON;
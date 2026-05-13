
-- 1. Insertion des Départements
INSERT INTO departements (nom, description) VALUES 
('Direction', 'Direction générale de l''entreprise'),
('Ressources Humaines', 'Gestion des talents et administration'),
('Développement', 'Équipe technique et ingénierie');

-- 2. Insertion des Types de Congé
INSERT INTO typesConge (libelle, joursAnnuels, deductible) VALUES 
('Congé Payé', 30, 1),
('RTT', 12, 1),
('Congé Sans Solde', 99, 0); -- Non déductible du solde principal

-- 3. Insertion des Employés 
-- Mots de passe fictifs (en situation réelle, utilisez password_hash() en PHP)
-- Tous les comptes ont pour mot de passe : "password123"
INSERT INTO employes (nom, prenom, email, password, role, DepartementId, dateEmbauche, actif) VALUES 
('Gérard', 'Alain', 'admin@techmada.com', '$2y$10$vK6kK3SOm/pYqXG.O7XUbuD98hO7O3Hl5KzWvWnQ5D8pZc8aY3eWG', 'admin', 1, '2022-01-15', 1),
('Dubois', 'Martine', 'martine.rh@techmada.com', '$2y$10$vK6kK3SOm/pYqXG.O7XUbuD98hO7O3Hl5KzWvWnQ5D8pZc8aY3eWG', 'rh', 2, '2023-03-01', 1),
('Martin', 'Jean', 'jean.rh@techmada.com', '$2y$10$vK6kK3SOm/pYqXG.O7XUbuD98hO7O3Hl5KzWvWnQ5D8pZc8aY3eWG', 'rh', 2, '2024-05-12', 1),
('Dupont', 'Lucas', 'lucas.dev@techmada.com', '$2y$10$vK6kK3SOm/pYqXG.O7XUbuD98hO7O3Hl5KzWvWnQ5D8pZc8aY3eWG', 'employe', 3, '2024-09-01', 1),
('Durand', 'Chloé', 'chloe.dev@techmada.com', '$2y$10$vK6kK3SOm/pYqXG.O7XUbuD98hO7O3Hl5KzWvWnQ5D8pZc8aY3eWG', 'employe', 3, '2025-01-10', 1),
('Ancien', 'Robert', 'robert.ex@techmada.com', '$2y$10$vK6kK3SOm/pYqXG.O7XUbuD98hO7O3Hl5KzWvWnQ5D8pZc8aY3eWG', 'employe', 3, '2020-05-20', 0); -- Compte inactif pour test

-- 4. Insertion des Soldes pour l'année 2026
-- On attribue les compteurs de congés pour l'année en cours aux employés actifs
INSERT INTO soldes (EmployeId, TypeCongeId, annee, joursAttribues, joursPris) VALUES 
-- Lucas Dupont (ID: 4) - A déjà pris 5 jours de Congés Payés (validés)
(4, 1, 2026, 30, 5), 
(4, 2, 2026, 12, 0),
-- Chloé Durand (ID: 5) - Solde tout neuf, rien pris
(5, 1, 2026, 30, 0), 
(5, 2, 2026, 12, 0),
-- Martine RH (ID: 2) - Les RH ont aussi droit à des congés !
(2, 1, 2026, 30, 0),
(2, 2, 2026, 12, 0);

-- 5. Insertion des Congés (Historique et demandes en cours)
INSERT INTO conges (EmployeId, TypeCongeId, dateDebut, dateFin, nbJours, motif, statut, commentaireRh, TraitePar) VALUES 
-- Une demande passée et APPROUVÉE pour Lucas (les 5 jours déjà déduits dans la table soldes)
(4, 1, '2026-03-02', '2026-03-06', 5, 'Vacances d''hiver', 'approuvee', 'Validé, bon repos.', 2),

-- Une demande REFUSÉE pour Lucas
(4, 2, '2026-04-10', '2026-04-10', 1, 'Pont personnel', 'refusee', 'Effectif réduit ce jour-là, désolé.', 2),

-- Une demande EN ATTENTE pour Lucas (le solde n''est pas encore touché !)
(4, 1, '2026-07-13', '2026-07-17', 5, 'Vacances d''été', 'enAttente', NULL, NULL),

-- Une demande EN ATTENTE pour Chloé
(5, 1, '2026-06-01', '2026-06-05', 5, 'Repos requis', 'enAttente', NULL, NULL);
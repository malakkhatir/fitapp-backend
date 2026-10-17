-- Base de données
DROP DATABASE IF EXISTS fitapp;
CREATE DATABASE fitapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fitapp;

-- Table utilisateurs
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  prenom VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,           -- hash bcrypt
  token VARCHAR(64) DEFAULT NULL,               -- token de session
  objectif_hebdo INT NOT NULL DEFAULT 3,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table exercices
CREATE TABLE exercises (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  categorie VARCHAR(50) NOT NULL,
  niveau VARCHAR(50) NOT NULL,
  duree INT NOT NULL,
  repetitions VARCHAR(100),
  description TEXT
) ENGINE=InnoDB;

-- Table programmes
CREATE TABLE programs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(100) NOT NULL,
  niveau VARCHAR(50) NOT NULL,
  duree INT NOT NULL
) ENGINE=InnoDB;

-- Lien programme ↔ exercices (ordre important)
CREATE TABLE program_exercises (
  program_id INT NOT NULL,
  exercise_id INT NOT NULL,
  ordre INT NOT NULL DEFAULT 0,
  PRIMARY KEY (program_id, exercise_id),
  FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
  FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table séances (historique de chaque utilisateur)
CREATE TABLE sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  programme_id INT DEFAULT NULL,
  nom_programme VARCHAR(100) NOT NULL,
  date DATETIME DEFAULT CURRENT_TIMESTAMP,
  minutes INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Données de départ (seed)
-- ============================================

INSERT INTO exercises (id, nom, categorie, niveau, duree, repetitions, description) VALUES
  (1, 'Squats', 'Renforcement', 'Débutant', 10, '3 séries de 12', 'Exercice pour les jambes et les fessiers.'),
  (2, 'Marche rapide', 'Cardio', 'Débutant', 20, '20 minutes', 'Marcher à un rythme soutenu et confortable.'),
  (3, 'Pompes adaptées', 'Renforcement', 'Débutant', 8, '3 séries de 8', 'Adapter la variante à son niveau.'),
  (4, 'Étirements doux', 'Mobilité', 'Débutant', 7, '7 minutes', 'Mouvements doux, sans forcer.');

INSERT INTO programs (id, titre, niveau, duree) VALUES
  (1, 'Découverte', 'Débutant', 20),
  (2, 'Cardio doux', 'Débutant', 25),
  (3, 'Renforcement', 'Débutant', 25);

INSERT INTO program_exercises (program_id, exercise_id, ordre) VALUES
  (1, 1, 1), (1, 4, 2),
  (2, 2, 1), (2, 4, 2),
  (3, 1, 1), (3, 3, 2), (3, 4, 3);


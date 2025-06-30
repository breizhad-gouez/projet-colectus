-- Création de la table pour les encombrants
CREATE TABLE IF NOT EXISTS encombrants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL,
    adresse_postale TEXT NOT NULL,
    type_encombrant VARCHAR(100) NOT NULL,
    consentement_rgpd ENUM('oui', 'non') NOT NULL DEFAULT 'non',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Index pour améliorer les performances
CREATE INDEX idx_nom_prenom ON encombrants(nom, prenom);
CREATE INDEX idx_email ON encombrants(email);
CREATE INDEX idx_type_encombrant ON encombrants(type_encombrant);

-- Exemple de données d'insertion pour tester
INSERT INTO encombrants (nom, prenom, telephone, email, adresse_postale, type_encombrant, consentement_rgpd) VALUES
('Dupont', 'Marie', '01.23.45.67.89', 'marie.dupont@email.com', '123 Rue de la Paix, 75001 Paris', 'Électroménager', 'oui'),
('Martin', 'Pierre', '01.98.76.54.32', 'pierre.martin@email.com', '456 Avenue des Champs, 75008 Paris', 'Mobilier', 'oui'),
('Durand', 'Sophie', '01.11.22.33.44', 'sophie.durand@email.com', '789 Boulevard Saint-Germain, 75006 Paris', 'Cartons', 'non');
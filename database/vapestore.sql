use VapeStore;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Identifiant unique
    nom VARCHAR(100) NOT NULL, -- Nom de l'utilisateur
    email VARCHAR(100) NOT NULL UNIQUE, -- Adresse email unique
    mot_de_passe VARCHAR(255) NOT NULL, -- Mot de passe sécurisé
    role ENUM('utilisateur', 'administrateur', 'support') DEFAULT 'utilisateur', -- Rôle de l'utilisateur
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Date d'inscription
);
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Identifiant unique
    nom VARCHAR(100) NOT NULL, -- Nom de la catégorie
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Date de création de la catégorie
);
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Identifiant unique
    nom VARCHAR(255) NOT NULL, -- Nom du produit
    description TEXT, -- Description détaillée du produit
    prix DECIMAL(10, 2) NOT NULL, -- Prix du produit
    stock INT NOT NULL DEFAULT 0, -- Quantité en stock
    categorie_id INT, -- Référence à la catégorie
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Date de création du produit
    FOREIGN KEY (categorie_id) REFERENCES categories(id) -- Clé étrangère vers la table categories
);
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Identifiant unique de la commande
    utilisateur_id INT NOT NULL, -- Référence à l'utilisateur ayant passé la commande
    montant_total DECIMAL(10, 2) NOT NULL, -- Montant total de la commande
    statut ENUM('en_attente', 'terminée', 'annulée') DEFAULT 'en_attente', -- Statut de la commande
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Date de création de la commande
    FOREIGN KEY (utilisateur_id) REFERENCES users(id) -- Clé étrangère vers la table users
);
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Identifiant unique
    commande_id INT NOT NULL, -- Référence à la commande
    produit_id INT NOT NULL, -- Référence au produit
    quantite INT NOT NULL, -- Quantité commandée
    prix DECIMAL(10, 2) NOT NULL, -- Prix du produit au moment de la commande
    FOREIGN KEY (commande_id) REFERENCES orders(id), -- Clé étrangère vers la table orders
    FOREIGN KEY (produit_id) REFERENCES products(id) -- Clé étrangère vers la table products
);
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Identifiant unique
    produit_id INT NOT NULL, -- Référence au produit
    utilisateur_id INT NOT NULL, -- Référence à l'utilisateur ayant laissé l'avis
    note INT CHECK(note BETWEEN 1 AND 5), -- Note entre 1 et 5
    commentaire TEXT, -- Commentaire de l'utilisateur
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Date de création de l'avis
    FOREIGN KEY (produit_id) REFERENCES products(id), -- Clé étrangère vers la table products
    FOREIGN KEY (utilisateur_id) REFERENCES users(id) -- Clé étrangère vers la table users
);
CREATE TABLE returns (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Identifiant unique
    commande_id INT NOT NULL, -- Référence à la commande
    produit_id INT NOT NULL, -- Référence au produit
    utilisateur_id INT NOT NULL, -- Référence à l'utilisateur ayant demandé le retour
    raison TEXT NOT NULL, -- Raison du retour
    statut ENUM('en_attente', 'accepté', 'rejeté') DEFAULT 'en_attente', -- Statut du retour
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Date de création de la demande
    FOREIGN KEY (commande_id) REFERENCES orders(id), -- Clé étrangère vers la table orders
    FOREIGN KEY (produit_id) REFERENCES products(id), -- Clé étrangère vers la table products
    FOREIGN KEY (utilisateur_id) REFERENCES users(id) -- Clé étrangère vers la table users
);
CREATE TABLE queries (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Identifiant unique
    utilisateur_id INT NOT NULL, -- Référence à l'utilisateur ayant posé la question
    message TEXT NOT NULL, -- Contenu de la requête
    reponse TEXT, -- Réponse à la requête
    statut ENUM('ouverte', 'répondue') DEFAULT 'ouverte', -- Statut de la requête
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Date de création de la requête
    FOREIGN KEY (utilisateur_id) REFERENCES users(id) -- Clé étrangère vers la table users
);
CREATE TABLE technical_issues (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Identifiant unique
    utilisateur_id INT NOT NULL, -- Référence à l'utilisateur ayant signalé le problème
    description TEXT NOT NULL, -- Description du problème
    statut ENUM('ouvert', 'en_cours', 'résolu') DEFAULT 'ouvert', -- Statut du problème
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Date de création du signalement
    FOREIGN KEY (utilisateur_id) REFERENCES users(id) -- Clé étrangère vers la table users
);
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Identifiant unique
    commande_id INT NOT NULL, -- Référence à la commande
    utilisateur_id INT NOT NULL, -- Référence à l'utilisateur ayant effectué le paiement
    montant DECIMAL(10, 2) NOT NULL, -- Montant payé
    methode ENUM('carte_credit', 'paypal', 'virement', 'espèces') DEFAULT 'carte_credit', -- Méthode de paiement
    statut ENUM('en_attente', 'terminé', 'échoué') DEFAULT 'en_attente', -- Statut du paiement
    date_transaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Date de la transaction
    FOREIGN KEY (commande_id) REFERENCES orders(id), -- Clé étrangère vers la table orders
    FOREIGN KEY (utilisateur_id) REFERENCES users(id) -- Clé étrangère vers la table users
);








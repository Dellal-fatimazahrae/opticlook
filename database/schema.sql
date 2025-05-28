-- Création de la base de données
CREATE DATABASE IF NOT EXISTS lunettes_ecommerce;
USE lunettes_ecommerce;

-- Table des utilisateurs
CREATE TABLE Users (
    User_ID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) UNIQUE NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    Password_Hash VARCHAR(255) NOT NULL,
    First_Name VARCHAR(50) NOT NULL,
    Last_Name VARCHAR(50) NOT NULL,
    Phone VARCHAR(20),
    Address TEXT,
    User_Type ENUM('Admin', 'Client') DEFAULT 'Client',
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Updated_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des catégories
CREATE TABLE Categories (
    Category_ID INT AUTO_INCREMENT PRIMARY KEY,
    Category_Name VARCHAR(100) NOT NULL,
    Description TEXT,
    Image_URL VARCHAR(255),
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des produits
CREATE TABLE Products (
    Product_ID INT AUTO_INCREMENT PRIMARY KEY,
    Product_Name VARCHAR(200) NOT NULL,
    Description TEXT,
    Price DECIMAL(10,2) NOT NULL,
    Stock_Quantity INT DEFAULT 0,
    Category_ID INT,
    Image_URL VARCHAR(255),
    Is_Featured BOOLEAN DEFAULT FALSE,
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Updated_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (Category_ID) REFERENCES Categories(Category_ID)
);

-- Table des commandes
CREATE TABLE Orders (
    Order_ID INT AUTO_INCREMENT PRIMARY KEY,
    User_ID INT,
    Total_Amount DECIMAL(10,2) NOT NULL,
    Order_Status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    Order_Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Shipping_Address TEXT,
    FOREIGN KEY (User_ID) REFERENCES Users(User_ID)
);

-- Table des détails de commande
CREATE TABLE Order_Details (
    Order_Detail_ID INT AUTO_INCREMENT PRIMARY KEY,
    Order_ID INT,
    Product_ID INT,
    Quantity INT NOT NULL,
    Unit_Price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (Order_ID) REFERENCES Orders(Order_ID),
    FOREIGN KEY (Product_ID) REFERENCES Products(Product_ID)
);

-- Insertion des données de test
INSERT INTO Categories (Category_Name, Description, Image_URL) VALUES
('Lunettes Adultes', 'Lunettes de vue pour adultes', 'assets/images/lunettes-adultes.jpg'),
('Lunettes Solaires', 'Lunettes de soleil tendance', 'assets/images/lunettes-solaires.jpg'),
('Accessoires', 'Accessoires pour lunettes', 'assets/images/accessoires.jpg');

INSERT INTO Users (Username, Email, Password_Hash, First_Name, Last_Name, User_Type) VALUES
('admin', 'admin@lunettes.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'System', 'Admin'),
('client1', 'client@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jean', 'Dupont', 'Client');

INSERT INTO Products (Product_Name, Description, Price, Stock_Quantity, Category_ID, Image_URL, Is_Featured) VALUES
('Lunettes Ray-Ban Classic', 'Lunettes de vue classiques Ray-Ban', 129.99, 15, 1, 'assets/images/rayban-classic.jpg', TRUE),
('Lunettes Oakley Sport', 'Lunettes de sport Oakley', 189.99, 8, 1, 'assets/images/oakley-sport.jpg', TRUE),
('Lunettes Solaires Aviator', 'Lunettes de soleil style aviateur', 89.99, 20, 2, 'assets/images/aviator.jpg', TRUE),
('Lunettes Solaires Wayfarer', 'Lunettes de soleil Wayfarer', 99.99, 12, 2, 'assets/images/wayfarer.jpg', TRUE),
('Étui à lunettes Premium', 'Étui de protection premium', 19.99, 50, 3, 'assets/images/etui-premium.jpg', FALSE),
('Chiffon microfibre', 'Chiffon de nettoyage microfibre', 5.99, 100, 3, 'assets/images/chiffon.jpg', FALSE);

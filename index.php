<?php
require 'includes/db.php';

// $database = new Database();
// $db = $database->getConnection();

// Récupérer les catégories
$query_categories = "SELECT * FROM Categories ORDER BY Category_ID";
$stmt_categories = $db->prepare($query_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les produits en vedette
$query_featured = "SELECT p.*, c.Category_Name FROM Products p 
                   LEFT JOIN Categories c ON p.Category_ID = c.Category_ID 
                   WHERE p.Is_Featured = 1 
                   ORDER BY p.Created_At DESC LIMIT 6";
$stmt_featured = $db->prepare($query_featured);
$stmt_featured->execute();
$featured_products = $stmt_featured->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lunettes & Style - E-commerce de Lunettes</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <i class="fas fa-glasses"></i>
                    <span>LunetteStyle</span>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php" class="nav-link active">Accueil</a></li>
                    <li><a href="produits.php" class="nav-link">Produits</a></li>
                    <li><a href="categories.php" class="nav-link">Catégories</a></li>
                    <li><a href="contact.php" class="nav-link">Contact</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo $_SESSION['user_type'] == 'Admin' ? 'admin/dashboard.php' : 'client/profile.php'; ?>" class="nav-link">Mon Compte</a></li>
                        <li><a href="logout.php" class="nav-link">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="connexion.php" class="nav-link">Connexion</a></li>
                    <?php endif; ?>
                </ul>
                <div class="hamburger">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Section Héro -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Lunettes & style, à portée de clic</h1>
                <p>Découvrez notre collection exclusive de lunettes tendance pour tous les styles</p>
                <a href="produits.php" class="btn-hero">Découvrir</a>
            </div>
            <div class="hero-image">
                <img src="assets/images/hero-image.png" alt="Personnes avec lunettes stylées" />
            </div>
        </div>
    </section>

    <!-- Nos catégories -->
    <section class="categories-section">
        <div class="container">
            <h2>Nos catégories de lunettes</h2>
            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-glasses"></i>
                    </div>
                    <h3>Lunettes adultes</h3>
                    <p>Collection complète pour adultes</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-sun"></i>
                    </div>
                    <h3>Lunettes solaires</h3>
                    <p>Protection et style garantis</p>
                </div>
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Accessoires</h3>
                    <p>Tout pour l'entretien</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Nouveautés -->
    <section class="products-section">
        <div class="container">
            <h2>Nouveautés</h2>
            <div class="products-grid">
                <div class="product-card">
                    <div class="product-image">
                        <img src="/placeholder.svg?height=200&width=200" alt="Lunettes Ray-Ban">
                        <div class="product-overlay">
                            <a href="#" class="btn-view">Voir détails</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3>Ray-Ban Classic</h3>
                        <p class="product-category">Lunettes adultes</p>
                        <div class="product-price">129,99€</div>
                        <div class="product-stock">
                            <span class="in-stock">En stock</span>
                        </div>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-image">
                        <img src="/placeholder.svg?height=200&width=200" alt="Lunettes Oakley">
                        <div class="product-overlay">
                            <a href="#" class="btn-view">Voir détails</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3>Oakley Sport</h3>
                        <p class="product-category">Lunettes adultes</p>
                        <div class="product-price">189,99€</div>
                        <div class="product-stock">
                            <span class="in-stock">En stock</span>
                        </div>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-image">
                        <img src="/placeholder.svg?height=200&width=200" alt="Lunettes Aviator">
                        <div class="product-overlay">
                            <a href="#" class="btn-view">Voir détails</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3>Aviator Classic</h3>
                        <p class="product-category">Lunettes solaires</p>
                        <div class="product-price">89,99€</div>
                        <div class="product-stock">
                            <span class="in-stock">En stock</span>
                        </div>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-image">
                        <img src="/placeholder.svg?height=200&width=200" alt="Lunettes Wayfarer">
                        <div class="product-overlay">
                            <a href="#" class="btn-view">Voir détails</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3>Wayfarer Style</h3>
                        <p class="product-category">Lunettes solaires</p>
                        <div class="product-price">99,99€</div>
                        <div class="product-stock">
                            <span class="in-stock">En stock</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="services-section">
        <div class="container">
            <h2>Nos Services sur Mesure</h2>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Satisfait ou remboursé</h3>
                    <p>Garantie de satisfaction à 100% ou remboursement intégral sous 30 jours</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Test de vue disponible</h3>
                    <p>Profitez de notre service de test de vue gratuit avec nos opticiens qualifiés</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-glasses"></i> LunetteStyle</h3>
                    <p>Votre spécialiste en lunettes de qualité depuis 2020</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="produits.php">Produits</a></li>
                        <li><a href="categories.php">Catégories</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Informations</h4>
                    <ul>
                        <li><a href="#">Mentions légales</a></li>
                        <li><a href="#">CGV</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
                    <p><i class="fas fa-envelope"></i> contact@lunettestyle.fr</p>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Rue de la Vision, 75001 Paris</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 LunetteStyle. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>

<?php
require_once '../includes/db.php';

// Vérifier si l'utilisateur est connecté et est admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    redirect('../connexion.php');
}

$database = new Database();
$db = $database->getConnection();

// Statistiques
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM Users WHERE User_Type = 'Client') as total_clients,
    (SELECT COUNT(*) FROM Products) as total_products,
    (SELECT COUNT(*) FROM Orders) as total_orders,
    (SELECT COALESCE(SUM(Total_Amount), 0) FROM Orders WHERE Order_Status != 'Cancelled') as total_revenue";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Commandes récentes
$recent_orders_query = "SELECT o.*, u.First_Name, u.Last_Name 
                       FROM Orders o 
                       LEFT JOIN Users u ON o.User_ID = u.User_ID 
                       ORDER BY o.Order_Date DESC LIMIT 5";
$recent_orders_stmt = $db->prepare($recent_orders_query);
$recent_orders_stmt->execute();
$recent_orders = $recent_orders_stmt->fetchAll(PDO::FETCH_ASSOC);

// Produits en rupture de stock
$low_stock_query = "SELECT * FROM Products WHERE Stock_Quantity <= 5 ORDER BY Stock_Quantity ASC";
$low_stock_stmt = $db->prepare($low_stock_query);
$low_stock_stmt->execute();
$low_stock_products = $low_stock_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - LunetteStyle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
            background: #f8f9fa;
        }
        
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2d5016 0%, #1e3a0f 100%);
            color: white;
            padding: 0;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
        
        .admin-logo {
            text-align: center;
            padding: 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
        }
        
        .admin-logo h2 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .admin-logo p {
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        .admin-nav {
            list-style: none;
            padding: 1rem 0;
        }
        
        .admin-nav li {
            margin-bottom: 0.5rem;
        }
        
        .admin-nav a {
            display: flex;
            align-items: center;
            padding: 1rem 2rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #4a7c2a;
        }
        
        .admin-nav a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        .admin-main {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }
        
        .admin-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-header h1 {
            color: #2d5016;
            font-size: 2.2rem;
            font-weight: 700;
        }
        
        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            background: #2d5016;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-color);
        }
        
        .stat-card.clients { --card-color: #007bff; }
        .stat-card.products { --card-color: #28a745; }
        .stat-card.orders { --card-color: #ffc107; }
        .stat-card.revenue { --card-color: #dc3545; }
        
        .stat-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: var(--card-color);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .admin-section {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .admin-section h3 {
            margin-bottom: 2rem;
            color: #2d5016;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .table th,
        .table td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-delivered { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        
        .stock-low { color: #dc3545; font-weight: bold; }
        .stock-medium { color: #ffc107; font-weight: bold; }
        .stock-good { color: #28a745; font-weight: bold; }
        
        .btn-admin {
            background: #2d5016;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-admin:hover {
            background: #1e3a0f;
            transform: translateY(-1px);
        }
        
        .btn-admin.btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-logo">
                <h2><i class="fas fa-glasses"></i> LunetteStyle</h2>
                <p>Panneau d'administration</p>
            </div>
            <ul class="admin-nav">
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fas fa-glasses"></i> Produits</a></li>
                <li><a href="categories.php"><i class="fas fa-tags"></i> Catégories</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                <li><a href="../index.php"><i class="fas fa-home"></i> Voir le site</a></li>
                <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-main">
            <div class="admin-header">
                <h1><i class="fas fa-chart-line"></i> Dashboard</h1>
                <div class="admin-user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['first_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Bienvenue, <?php echo htmlspecialchars($_SESSION['first_name']); ?></div>
                        <div style="font-size: 0.9rem; color: #666;">Administrateur</div>
                    </div>
                </div>
            </div>
            
            <!-- Statistiques -->
            <div class="stats-grid">
                <div class="stat-card clients">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><?php echo $stats['total_clients']; ?></div>
                    <div class="stat-label">Clients inscrits</div>
                </div>
                <div class="stat-card products">
                    <div class="stat-icon"><i class="fas fa-glasses"></i></div>
                    <div class="stat-number"><?php echo $stats['total_products']; ?></div>
                    <div class="stat-label">Produits en catalogue</div>
                </div>
                <div class="stat-card orders">
                    <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-number"><?php echo $stats['total_orders']; ?></div>
                    <div class="stat-label">Commandes totales</div>
                </div>
                <div class="stat-card revenue">
                    <div class="stat-icon"><i class="fas fa-euro-sign"></i></div>
                    <div class="stat-number"><?php echo number_format($stats['total_revenue'], 0, ',', ' '); ?>€</div>
                    <div class="stat-label">Chiffre d'affaires</div>
                </div>
            </div>
            
            <!-- Commandes récentes -->
            <div class="admin-section">
                <h3><i class="fas fa-clock"></i> Commandes récentes</h3>
                <?php if (count($recent_orders) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Commande</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td><strong>#<?php echo str_pad($order['Order_ID'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                            <td><?php echo htmlspecialchars($order['First_Name'] . ' ' . $order['Last_Name']); ?></td>
                            <td><strong><?php echo number_format($order['Total_Amount'], 2, ',', ' '); ?>€</strong></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($order['Order_Status']); ?>">
                                    <?php echo $order['Order_Status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y à H:i', strtotime($order['Order_Date'])); ?></td>
                            <td>
                                <a href="order-detail.php?id=<?php echo $order['Order_ID']; ?>" class="btn-admin btn-sm">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Aucune commande récente</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Produits en rupture de stock -->
            <div class="admin-section">
                <h3><i class="fas fa-exclamation-triangle"></i> Alertes stock</h3>
                <?php if (count($low_stock_products) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Stock restant</th>
                            <th>Prix unitaire</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($low_stock_products as $product): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($product['Product_Name']); ?></strong></td>
                            <td>
                                <span class="<?php echo $product['Stock_Quantity'] == 0 ? 'stock-low' : ($product['Stock_Quantity'] <= 2 ? 'stock-low' : 'stock-medium'); ?>">
                                    <i class="fas fa-box"></i> <?php echo $product['Stock_Quantity']; ?> unité<?php echo $product['Stock_Quantity'] > 1 ? 's' : ''; ?>
                                </span>
                            </td>
                            <td><strong><?php echo number_format($product['Price'], 2, ',', ' '); ?>€</strong></td>
                            <td>
                                <a href="product-edit.php?id=<?php echo $product['Product_ID']; ?>" class="btn-admin btn-sm">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>Tous les produits sont bien approvisionnés</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
require_once 'includes/db.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Connexion
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];
        
        // $database = new Database();
        // $db = $database->getConnection();
        
        $query = "SELECT User_ID, Username, Email, Password_Hash, First_Name, Last_Name, User_Type FROM Users WHERE Email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['Password_Hash'])) {
            $_SESSION['user_id'] = $user['User_ID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['user_type'] = $user['User_Type'];
            $_SESSION['first_name'] = $user['First_Name'];
            
            if ($user['User_Type'] == 'Admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            $error_message = "Email ou mot de passe incorrect.";
        }
    } elseif (isset($_POST['register'])) {
        // Inscription
        $username = sanitize_input($_POST['username']);
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $first_name = sanitize_input($_POST['first_name']);
        $last_name = sanitize_input($_POST['last_name']);
        
        if ($password !== $confirm_password) {
            $error_message = "Les mots de passe ne correspondent pas.";
        } else {
            // $database = new Database();
            // $db = $database->getConnection();
            
            // Vérifier si l'email existe déjà
            $check_query = "SELECT User_ID FROM Users WHERE Email = ? OR Username = ?";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->execute([$email, $username]);
            
            if ($check_stmt->rowCount() > 0) {
                $error_message = "Cet email ou nom d'utilisateur existe déjà.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $insert_query = "INSERT INTO Users (Username, Email, Password_Hash, First_Name, Last_Name) VALUES (?, ?, ?, ?, ?)";
                $insert_stmt = $db->prepare($insert_query);
                
                if ($insert_stmt->execute([$username, $email, $password_hash, $first_name, $last_name])) {
                    $success_message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                } else {
                    $error_message = "Erreur lors de l'inscription.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - LunetteStyle</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2d5016 0%, #4a7c2a 100%);
            padding: 2rem;
        }
        
        .auth-box {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h1 {
            color: #2d5016;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .auth-tabs {
            display: flex;
            margin-bottom: 2rem;
            border-bottom: 1px solid #eee;
        }
        
        .auth-tab {
            flex: 1;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .auth-tab.active {
            border-bottom-color: #2d5016;
            color: #2d5016;
        }
        
        .auth-form {
            display: none;
        }
        
        .auth-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #2d5016;
        }
        
        .btn-auth {
            width: 100%;
            background: #2d5016;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-auth:hover {
            background: #1e3a0f;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .back-link {
            text-align: center;
            margin-top: 2rem;
        }
        
        .back-link a {
            color: #2d5016;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .back-link a:hover {
            color: #1e3a0f;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1><i class="fas fa-glasses"></i> LunetteStyle</h1>
                <p>Bienvenue sur votre espace personnel</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <div class="auth-tabs">
                <div class="auth-tab active" onclick="switchTab('login')">Connexion</div>
                <div class="auth-tab" onclick="switchTab('register')">Inscription</div>
            </div>
            
            <!-- Formulaire de connexion -->
            <form class="auth-form active" id="login-form" method="POST">
                <div class="form-group">
                    <label for="login-email">Adresse email</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Mot de passe</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <button type="submit" name="login" class="btn-auth">Se connecter</button>
            </form>
            
            <!-- Formulaire d'inscription -->
            <form class="auth-form" id="register-form" method="POST">
                <div class="form-group">
                    <label for="register-username">Nom d'utilisateur</label>
                    <input type="text" id="register-username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Adresse email</label>
                    <input type="email" id="register-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="register-first-name">Prénom</label>
                    <input type="text" id="register-first-name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="register-last-name">Nom de famille</label>
                    <input type="text" id="register-last-name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Mot de passe</label>
                    <input type="password" id="register-password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="register-confirm-password">Confirmer le mot de passe</label>
                    <input type="password" id="register-confirm-password" name="confirm_password" required>
                </div>
                <button type="submit" name="register" class="btn-auth">S'inscrire</button>
            </form>
            
            <div class="back-link">
                <a href="index.php"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tab) {
            // Gérer les onglets
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
            
            if (tab === 'login') {
                document.querySelector('.auth-tab:first-child').classList.add('active');
                document.getElementById('login-form').classList.add('active');
            } else {
                document.querySelector('.auth-tab:last-child').classList.add('active');
                document.getElementById('register-form').classList.add('active');
            }
        }
    </script>
</body>
</html>

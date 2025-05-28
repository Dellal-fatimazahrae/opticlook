<?php
 
$serv='mysql:host=localhost;dbname=lunettes_ecommerce';
$user='root';
$pass='';
// $message='';
        try{
                  $db=new PDO($serv,$user,$pass);
                  $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                //   $message='you connected';
       }catch(PDOException $e){
        echo $e->getMessage();
       }


// class Database {
//     private $host = 'localhost';
//     private $db_name = 'lunettes_ecommerce';
//     private $username = 'root';
//     private $password = '';
//     private $conn;

//     public function getConnection() {
//         $this->conn = null;
//         try {
//             $this->conn = new PDO(
//                 "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
//                 $this->username,
//                 $this->password
//             );
//             $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//             $this->conn->exec("set names utf8");
//         } catch(PDOException $exception) {
//             echo "Erreur de connexion: " . $exception->getMessage();
//         }
//         return $this->conn;
//     }
// }

// Fonctions utilitaires
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Démarrer la session
session_start();
?>
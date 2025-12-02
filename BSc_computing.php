<?php
session_start();
?>
<?php
include 'config.php';

try {
    $pdo = new PDO($dsn, $user, $pass);
    
    $stmt = $pdo->prepare("SELECT * FROM comp");
    $stmt->execute();
    
    $comps = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
} catch (PDOException $e) {
    $comps = [];
    
}

include './templates/BSc_computing.html.php';
?>

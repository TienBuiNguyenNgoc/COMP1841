<?php
session_start();
?>
<?php
include 'config.php';

try {
    $pdo = new PDO($dsn, $user, $pass);
    
    $stmt = $pdo->prepare("SELECT * FROM busi");
    $stmt->execute();
    
    $busis = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
} catch (PDOException $e) {
    $busis = [];
    
}

include './templates/BA_business.html.php';
?>

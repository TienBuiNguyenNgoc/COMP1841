<?php
session_start();
?>
<?php
include 'config.php';

try {
    $pdo = new PDO($dsn, $user, $pass);
    
    $stmt = $pdo->prepare("SELECT * FROM desi");
    $stmt->execute();
    
    $desis = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
} catch (PDOException $e) {
    $desis = [];
    
}

include './templates/BA_graphic_and_digital_design.html.php';
?>

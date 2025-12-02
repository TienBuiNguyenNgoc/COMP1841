<?php
header("Content-Type: application/json");
include './config.php';
$pdo = new PDO($dsn, $user, $pass);

$subject = $_GET['subject'] ?? '';
$allowed = ['comp', 'busi', 'desi'];

if (!in_array($subject, $allowed)) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->query("SELECT Modulecode, Modulename FROM $subject ORDER BY Modulecode ASC");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

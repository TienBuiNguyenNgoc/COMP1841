<?php
session_start();
include 'config.php';

if (empty($_SESSION['Username'])) {
    die("Not logged in.");
}

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$username = $_SESSION['Username'];

if (!isset($_FILES['avatar'])) {
    die("No file uploaded.");
}

$img = $_FILES['avatar'];
$allowed = ['jpg','jpeg','png','gif','webp'];
$ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    die("Invalid file type.");
}

$filename = "avt_" . $username . "_" . time() . "." . $ext;
$uploadPath = "avatar/" . $filename;

move_uploaded_file($img['tmp_name'], $uploadPath);

$stmt = $pdo->prepare("UPDATE users SET Avatar = :avt WHERE Username = :u");
$stmt->execute([
    ':avt' => $filename,
    ':u' => $username
]);

$_SESSION['Avatar'] = $filename;

header("Location: profile.php");
exit;

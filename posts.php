<?php
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: templates/login.html.php");
    exit;
}

include '../config.php';

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$userid = (int)$_SESSION['userID']; 
$subject = $_POST['subject'] ?? null;
$moduleOptions = [];
$message = "";
$error = "";

if ($subject) {
    $stmt = $pdo->prepare("SELECT moduleID, modulecode, modulename FROM modules 
                           WHERE subject = ? 
                           ORDER BY modulecode ASC");
    $stmt->execute([$subject]);
    $moduleOptions = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'])) {

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $moduleID = isset($_POST['modulecode']) ? (int)$_POST['modulecode'] : null;

    if (!$subject) {
        $error = "Please choose a subject.";
    } elseif (!$moduleID) {
        $error = "Please choose a module.";
    } elseif ($title === "" || $content === "") {
        $error = "Title and content cannot be empty.";
    }

    $imagePath = null;
    if (empty($error) && !empty($_FILES['image']['name'])) {

        $targetDir = "../upload/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = "upload/" . $fileName;
        } else {
            $error = "Failed to upload image.";
        }
    }

    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO posts (userID, title, subject, moduleID, content, image, created)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([$userid, $title, $subject, $moduleID, $content, $imagePath]);

            $message = "Post created successfully!";
            $subject = null;
            $moduleOptions = [];

        } catch (Exception $e) {
            $error = "DB connection failed: " . $e->getMessage();
        }
    }
}

$subjects = [
    'comp' => 'COMP',
    'busi' => 'BUSI',
    'desi' => 'DESI'
];
?>

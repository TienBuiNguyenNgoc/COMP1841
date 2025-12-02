<?php
session_start();
include './config.php';

$pdo = new PDO($dsn, $user, $pass);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['Username'])) {
        die("You must be logged in to submit a post.");
    }
    $username = $_SESSION['Username'];

    $title = trim($_POST['title']);
    $subject = $_POST['subject'] ?? '';
    $modulecode = $_POST['modulecode'] ?? '';
    $content = trim($_POST['content']);
    $imagePath = null;

    if (empty($title) || empty($subject) || empty($modulecode) || empty($content)) {
        die("Please fill all required fields.");
    }

    if (!empty($_FILES['image']['name'])) {

        $targetDir = "./upload/";

        $imageName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . uniqid() . "_" . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;  
        } else {
            die("Image upload failed.");
        }
    }

    $sql = "
        INSERT INTO posts (username, title, subject, modulecode, content, image)
        VALUES (:username, :title, :subject, :modulecode, :content, :image)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':username' => $username,
        ':title' => $title,
        ':subject' => $subject,
        ':modulecode' => $modulecode,
        ':content' => $content,
        ':image' => $imagePath
    ]);

    echo "Post successfully created!";
} 
else {
    echo "Invalid request.";
}
?>

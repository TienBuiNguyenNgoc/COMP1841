<?php
session_start();
include 'config.php';

if (!isset($_SESSION['userID'])) {
    die("Please log in to comment.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postID  = (int)$_POST['postID'];
    $userID  = $_SESSION['userID'];
    $content = trim($_POST['content']);

    if (!empty($postID) && !empty($content)) {
        try {
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("INSERT INTO replies (postID, userID, content, created) 
                                    VALUES (:postID, :userID, :content, NOW())"); 
            $stmt->execute([
                ':postID'  => $postID,
                ':userID'  => $userID,
                ':content' => $content
            ]);
            header("Location: view_post.php?postID=" . $postID);
            exit;

        } catch (PDOException $e) {
            echo "DB connection failed: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('Content cannot be empty.'); window.history.back();</script>";
    }
} else {
    header("Location: index.php");
}
?>
<?php
session_start();
include 'config.php';
if (!isset($_SESSION['userID'])) {
    die("You need to log in.");
}

if (isset($_GET['replyID'])) {
    $replyID = (int)$_GET['replyID'];
    $userID  = $_SESSION['userID'];

    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $check = $pdo->prepare("SELECT postID FROM replies WHERE replyID = :rid AND userID = :uid");
        $check->execute([':rid' => $replyID, ':uid' => $userID]);
        $reply = $check->fetch();

        if ($reply) {
            $stmt = $pdo->prepare("DELETE FROM replies WHERE replyID = :rid");
            $stmt->execute([':rid' => $replyID]);
            
            header("Location: view_post.php?postID=" . $reply['postID']);
            exit;
        } else {
            die("You do not have permission to delete this comment, or the comment does not exist.");
        }

    } catch (PDOException $e) {
        die("DB connection failed: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
}
?>
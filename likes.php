<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    echo json_encode(['status' => 'error', 'message' => 'You need to log in to like.']);
    exit;
}

$userID = $_SESSION['userID'];
$input = json_decode(file_get_contents('php://input'), true);
$postID = isset($input['postID']) ? (int)$input['postID'] : 0;

if ($postID <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'The post is invalid.']);
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $checkStmt = $pdo->prepare("SELECT likeID FROM likes WHERE postID = :postID AND userID = :userID");
    $checkStmt->execute([':postID' => $postID, ':userID' => $userID]);
    $existingLike = $checkStmt->fetch();

    if ($existingLike) {
        $delStmt = $pdo->prepare("DELETE FROM likes WHERE postID = :postID AND userID = :userID");
        $delStmt->execute([':postID' => $postID, ':userID' => $userID]);
        $action = 'unliked';
    } else {
        $insStmt = $pdo->prepare("INSERT INTO likes (postID, userID) VALUES (:postID, :userID)");
        $insStmt->execute([':postID' => $postID, ':userID' => $userID]);
        $action = 'liked';
    }

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE postID = :postID");
    $countStmt->execute([':postID' => $postID]);
    $newCount = $countStmt->fetchColumn();

    echo json_encode([
        'status' => 'success',
        'action' => $action,
        'new_count' => $newCount
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
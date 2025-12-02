<?php
session_start();
include 'config.php';

if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit;
}

$pdo = new PDO($dsn, $user, $pass);
$message = "";
$replyData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $replyID = (int)$_POST['replyID'];
    $content = trim($_POST['content']);
    $userID  = $_SESSION['userID'];

    if (!empty($content)) {
        $stmt = $pdo->prepare("UPDATE replies SET content = :content WHERE replyID = :rid AND userID = :uid");
        $stmt->execute([
            ':content' => $content, 
            ':rid' => $replyID, 
            ':uid' => $userID
        ]);
        
        $stmt = $pdo->prepare("SELECT postID FROM replies WHERE replyID = :rid");
        $stmt->execute([':rid' => $replyID]);
        $post = $stmt->fetch();

        header("Location: view_post.php?postID=" . $post['postID']);
        exit;
    } else {
        $message = "The content cannot empty.";
    }
}

if (isset($_GET['replyID'])) {
    $replyID = (int)$_GET['replyID'];
    $userID  = $_SESSION['userID'];

    $stmt = $pdo->prepare("SELECT * FROM replies WHERE replyID = :rid AND userID = :uid");
    $stmt->execute([':rid' => $replyID, ':uid' => $userID]);
    $replyData = $stmt->fetch();

    if (!$replyData) {
        die("Comment not found or you do not have permission to edit.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Reply</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #0F1C2C; color: white; }</style>
</head>
<body>
    <div class="container mt-5" style="max-width: 600px;">
        <h3>Edit Your Comment</h3>
        
        <?php if($message): ?>
            <div class="alert alert-danger"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" class="bg-secondary p-4 rounded text-dark">
            <input type="hidden" name="replyID" value="<?= $replyData['replyID'] ?>">
            
            <div class="mb-3">
                <label class="form-label text-white">Content</label>
                <textarea name="content" class="form-control" rows="4" required><?= htmlspecialchars($replyData['content']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-warning">Save Changes</button>
            <a href="view_post.php?postID=<?= $replyData['postID'] ?>" class="btn btn-light">Cancel</a>
        </form>
    </div>
</body>
</html>
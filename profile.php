<?php
session_start();
include 'config.php';

if (empty($_SESSION['Username'])) {
    header("Location: templates/login.html.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

$username = $_SESSION['Username'];

$userStmt = $pdo->prepare("
    SELECT UserID, Username, Email, Avatar 
    FROM users 
    WHERE Username = :u
");
$userStmt->execute([':u' => $username]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$userID = $user['UserID'];
$avatarFile = $user['Avatar'] ? "avatar/" . $user['Avatar'] : "avatar/default.png";

if (isset($_GET['delete_id'])) {
    $deleteID = (int)$_GET['delete_id'];
    $check = $pdo->prepare("
        SELECT 1 
        FROM posts 
        WHERE postID = :id AND userID = :uid
    ");
    $check->execute([
        ':id' => $deleteID,
        ':uid' => $userID
    ]);

    if ($check->fetch()) {
        $pdo->prepare("DELETE FROM posts WHERE postID = :id")
            ->execute([':id' => $deleteID]);
    }

    header("Location: profile.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT postID, title, moduleID, image, created
    FROM posts
    WHERE userID = :uid
    ORDER BY created DESC
");
$stmt->execute([':uid' => $userID]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.html.php';
?>

<nav class="underline-nav" style="justify-content: center; align-items: center; background-color: #2E3D54;"> 
    <img src="img_bg/Logo.png" alt="Logo" width="200" height="40">
    <a href="index.php">Home</a> 
    <a href="contact/contact.html.php">Contact</a> 
</nav>

<div class="container mt-5" style="color:white;">

    <h2>User Profile</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-dark text-white p-3">
                <h4><?= htmlspecialchars($user['Username']) ?></h4>
                <p><?= htmlspecialchars($user['Email']) ?></p>

                <img src="<?= $avatarFile ?>" 
                    width="150" height="150"
                    style="border-radius: 50%; object-fit: cover; display:block; margin:auto;">

                <form action="upload_avt.php" method="POST" enctype="multipart/form-data" class="mt-3">
                    <input type="file" name="avatar" class="form-control" required>
                    <button type="submit" class="btn btn-primary w-100 mt-2">Change Avatar</button>
                </form>
            </div>
        </div>

      <div class="col-md-8">
        <h3 class="mb-4 border-bottom pb-2">General Settings</h3>

        <div class="list-group">
            
            <a href="my_posts.php" class="list-group-item list-group-item-action list-group-item-dark d-flex justify-content-between align-items-center p-3 mb-2 rounded">
                <div>
                    <i class="bi bi-file-earmark-text me-2 fs-5"></i> 
                    <span class="fw-bold">My Posts</span>
                    <span class="badge bg-primary rounded-pill ms-2"><?= count($posts) ?></span>
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    </div>
</div>

</body>
</html>

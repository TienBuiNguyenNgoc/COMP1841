<?php
session_start();
include 'config.php';

if (empty($_SESSION['userID'])) {
    header("Location: templates/login.html.php");
    exit;
}

$userid = (int)$_SESSION['userID'];

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

if (isset($_GET['delete_id'])) {
    $deleteID = (int)$_GET['delete_id'];

    $check = $pdo->prepare("
        SELECT 1 FROM posts 
        WHERE postID = :pid AND userID = :uid
    ");
    $check->execute([
        ':pid' => $deleteID,
        ':uid' => $userid
    ]);

    if ($check->fetch()) {
        $delete = $pdo->prepare("DELETE FROM posts WHERE postID = :pid");
        $delete->execute([':pid' => $deleteID]);
    }

    header("Location: my_posts.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.postID, p.userID, p.title, m.subject, p.moduleID, p.image, p.created,
           m.modulecode
    FROM posts p
    LEFT JOIN modules m ON p.moduleID = m.moduleID
    WHERE p.userID = :uid
    ORDER BY p.created DESC
");

$stmt->execute([':uid' => $userid]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.html.php';
?>

<nav class="underline-nav" style="justify-content: center; align-items: center; background-color: #2E3D54;"> 
    <img src="img_bg/Logo.png" alt="Logo" width="200">
    <a href="index.php">Home</a> 
    <a href="contact/contact.html.php">Contact</a> 
</nav>

<div class="container mt-5">
    <h2 class="mb-4" style="color: #fff;">My Posts</h2>

    <?php if (count($posts) === 0): ?>
        <p class="text-secondary">You haven't created any posts yet.</p>
    <?php else: ?>

    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Subject</th>
                <th>Module</th>
                <th>Image</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($posts as $post): ?>
            <tr>
                <td><?= $post['postID'] ?></td>
                <td><?= htmlspecialchars($post['title']) ?></td>
                
                <td><?= htmlspecialchars($post['subject'] ?? 'N/A') ?></td>
                
                <td><?= htmlspecialchars($post['modulecode'] ?? 'N/A') ?></td>

                <td>
                    <?php if (!empty($post['image'])): ?>
                        <img src="<?= htmlspecialchars($post['image']) ?>" 
                             width="80" height="50" style="object-fit:cover;">
                    <?php else: ?>
                        <span class="text-secondary">No image</span>
                    <?php endif; ?>
                </td>

                <td><?= $post['created'] ?></td>

                <td>
                    <a href="edit_my_post.php?postID=<?= $post['postID'] ?>"
                       class="btn btn-sm btn-warning">Edit</a>

                    <a href="my_posts.php?delete_id=<?= $post['postID'] ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete this post?')">
                       Delete
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php endif; ?>
</div>

</body>
</html>
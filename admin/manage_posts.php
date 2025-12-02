<?php
session_start();
include '../config.php';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
if (empty($_SESSION['Role']) || $_SESSION['Role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE postID = :id"); 
    $stmt->execute([':id' => $delete_id]);
    header('Location: manage_posts.php');
    exit;
}

$sql = "SELECT p.postID, p.title, p.image, p.created, 
               m.subject, m.modulecode 
        FROM posts p
        JOIN modules m ON p.moduleID = m.moduleID
        ORDER BY p.created DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(); 
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0F1C2C;
            color: #fff;
        }
        .underline-nav {
            display: flex;
            gap: 30px;
        }

        .underline-nav a {
            position: relative;
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 0;
            transition: .5s ease;
        }

        .underline-nav a:hover {
            color: #FF004D;
        }

        .underline-nav a::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background-color: #FF004D;
            transition: width 0.3s ease;
        }

        .underline-nav a:hover::after {
            width: 100%;
        } 
    </style>
</head>
<body>
<nav class="underline-nav px-4" style="background-color: #2E3D54; align-items:center;">
    <img src="../img_bg/Logo.png" alt="Bootstrap" width="200" height="40">
    <a href="../index.php">Home</a>
    <a href="dashboard.php">Dashboard</a> <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</nav>

<div class="container mt-5">
    <h1 class="mb-4 text-warning">Manage Posts</h1>

    <a href="../create_post.php" class="btn btn-success mb-3 ms-2">Add New Post</a>

    <table class="table table-dark table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Subject</th>
                <th>Module</th>
                <th>Image</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?= htmlspecialchars($post['postID']) ?></td>
                        <td><?= htmlspecialchars($post['title']) ?></td>
                        
                        <td>
                            <span class="badge bg-info text-dark">
                                <?= htmlspecialchars($post['subject'] ?? 'N/A') ?>
                            </span>
                        </td>
                        
                        <td><?= htmlspecialchars($post['modulecode'] ?? 'N/A') ?></td>
                        
                        <td>
                            <?php if (!empty($post['image'])): ?>
                                <img src="../<?= htmlspecialchars($post['image']) ?>" width="80" height="50" style="object-fit:cover;" class="rounded">
                            <?php else: ?>
                                <span class="text-secondary">No image</span>
                            <?php endif; ?>
                        </td>
                        
                        <td><?= htmlspecialchars($post['created']) ?></td>
                        
                        <td>
                            <a href="edit_post.php?postID=<?= $post['postID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            
                            <a href="manage_posts.php?delete_id=<?= $post['postID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-secondary">No posts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
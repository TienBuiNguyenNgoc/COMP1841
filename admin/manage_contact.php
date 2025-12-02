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

$stmt = $pdo->query("SELECT * FROM contacts ORDER BY contactID DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    $stmt = $pdo->prepare("SELECT image FROM contacts WHERE contactID = :id");
    $stmt->execute([':id' => $delete_id]);
    $contact = $stmt->fetch();

    if ($contact && !empty($contact['image'])) {
        $filePath = "../upload/" . $contact['image'];
        if (file_exists($filePath)) {
            unlink($filePath); 
        }
    }

    $stmt = $pdo->prepare("DELETE FROM contacts WHERE contactID = :id");
    $stmt->execute([':id' => $delete_id]);
    
    header('Location: manage_contact.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Contact Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        .badge { 
            padding:5px 10px; 
            border-radius:4px; 
            color:#fff; 
        }
        .badge.ok { 
            background-color: #198754; 
        } 
        .badge.no { 
            background-color: #dc3545;
        } 
    </style>
</head>
<body>

<nav class="underline-nav px-4" style="background-color: #2E3D54; align-items:center;">
    <img src="../img_bg/Logo.png" alt="Logo" width="200" height="40">
    <a href="../index.php">Home</a>
    <a href="dashboard.php">Dashboard</a>
</nav>

<div class="container mt-5">
    <h1 class="mb-4 text-warning">Manage Contact Messages</h1>

    <table class="table table-dark table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Sender Email</th>
                <th>Message</th>
                <th>Image</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($messages) > 0): ?>
                <?php foreach ($messages as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['contactID']) ?></td>
                        <td><?= htmlspecialchars($row['sender_email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                        <td>
                            <?php if (!empty($row['image'])): ?>
                                <img src="../upload/<?= htmlspecialchars($row['image']) ?>" width="80" height="50" style="object-fit:cover;" class="rounded">
                            <?php else: ?>
                                <span class="text-secondary">No image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['reply_message'])): ?>
                                <span class="badge ok">Replied</span><br>
                                <small class="text-light"><?= htmlspecialchars($row['reply_date']) ?></small>
                            <?php else: ?>
                                <span class="badge no">Not Replied</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="../contact/reply_contact.php?contactID=<?= $row['contactID'] ?>" class="btn btn-sm btn-info">Reply</a>
                            <a href="manage_contact.php?delete_id=<?= $row['contactID'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this message and any associated image?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-secondary">No contact messages found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
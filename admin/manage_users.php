<?php
session_start();
include '../config.php';
$pdo = new PDO($dsn, $user, $pass);

if (empty($_SESSION['Role']) || $_SESSION['Role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if (isset($_POST['update_role'])) {
    $userId = intval($_POST['UserID']);
    $newRole = $_POST['role'];

    $allowedRoles = ['user', 'admin'];
    if (in_array($newRole, $allowedRoles, true)) {
        $stmt = $pdo->prepare("UPDATE users SET Role = :role WHERE UserID = :UserID");
        $stmt->execute([':role' => $newRole, ':UserID' => $userId]);
    }
    header('Location: manage_users.php');
    exit;
}

if (isset($_POST['delete_user'])) {
    $userId = intval($_POST['UserID']);
    $stmt = $pdo->prepare("DELETE FROM users WHERE userID = :UserID");
    $stmt->execute([':UserID' => $userId]);
    header('Location: manage_users.php');
    exit;
}

$stmt = $pdo->query("SELECT UserID, Username, Email, Role FROM users ORDER BY UserID ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
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

    .btn {
        background-color: #fff; 
        color: #000;
    }
    .btn:hover {
        background-color: #FF004D; 
        color: #fff;
    }
  </style>
</head>
<body class="bg-dark text-light">
<nav class="underline-nav" style="background-color: #2E3D54;">
    <img src="../img_bg/Logo.png" alt="Bootstrap" width="200" height="40">
    <a href="../index.php">Home</a>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </nav>
<div class="container mt-5">
  <h2 class="mb-4">User Management</h2>

  <table class="table table-dark table-striped table-bordered align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?= htmlspecialchars($user['UserID']) ?></td>
          <td><?= htmlspecialchars($user['Username']) ?></td>
          <td><?= htmlspecialchars($user['Email']) ?></td>
          <td>
            <form method="post" class="d-flex">
              <input type="hidden" name="UserID" value="<?= $user['UserID'] ?>">
              <select name="role" class="form-select form-select-sm me-2">
                <option value="user" <?= $user['Role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['Role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
              </select>
              <button type="submit" name="update_role" class="btn btn-warning btn-sm">Update</button>
            </form>
          </td>
          <td>
            <?php if ($_SESSION['Username'] !== $user['Username']): ?>
              <form method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                <input type="hidden" name="UserID" value="<?= $user['UserID'] ?>">
                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
              </form>
            <?php else: ?>
              <span class="text-secondary">Your Account</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>

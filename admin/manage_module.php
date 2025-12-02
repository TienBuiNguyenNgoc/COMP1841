<?php
session_start();
include '../config.php';
$pdo = new PDO($dsn, $user, $pass);

if (empty($_SESSION['Role']) || $_SESSION['Role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$message = "";

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM modules WHERE moduleID = :moduleID")->execute([':moduleID' => $id]);
    $message = '<div class="alert alert-success">Module deleted successfully!</div>';
}

if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['modulename']);
    $code = trim($_POST['modulecode']);
    $subject = $_POST['subject'];

    if ($name && $code) {
        $stmt = $pdo->prepare("INSERT INTO modules (modulename, modulecode, subject)
                               VALUES (:n, :c, :s)");
        $stmt->execute([':n'=>$name, ':c'=>$code, ':s'=>$subject]);
        $message = '<div class="alert alert-success">Module added!</div>';
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['moduleID']);
    $name = trim($_POST['modulename']);
    $code = trim($_POST['modulecode']);
    $subject = $_POST['subject'];

    $stmt = $pdo->prepare("UPDATE modules 
                           SET modulename = :n, modulecode = :c, subject = :s
                           WHERE moduleID = :moduleID");
    $stmt->execute([':n'=>$name, ':c'=>$code, ':s'=>$subject, ':moduleID'=>$id]);
    $message = '<div class="alert alert-success">Module updated!</div>';
}

$filter = $_GET['filter'] ?? 'all';

if ($filter === 'all') {
    $modules = $pdo->prepare("SELECT * FROM modules ORDER BY moduleID DESC")->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT * FROM modules WHERE subject = :s ORDER BY moduleID DESC");
    $stmt->execute([':s' => $filter]);
    $modules = $stmt->fetchAll();
}

$editData = null;

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM modules WHERE moduleID = :moduleID");
    $stmt->execute([':moduleID'=>$id]);
    $editData = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Modules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color:#0F1C2C; color:white; }
.underline-nav { display:flex; gap:30px; align-items:center; padding:15px 30px; }
.underline-nav a { color:white; text-decoration:none; font-size:18px; position:relative; }
.underline-nav a:hover { color:#FF004D; }
.underline-nav a::after {
    content:''; position:absolute; left:0; bottom:0; height:3px; width:0;
    background:#FF004D; transition:.3s;
}
.underline-nav a:hover::after { width:100%; }
.btn-custom { background:white; color:black; }
.btn-custom:hover { background:#FF004D; color:white; }
</style>
</head>
<body>

<nav class="underline-nav" style="background:#2E3D54;">
    <img src="../img_bg/Logo.png" width="200">
    <a href="../index.php">Home</a>
</nav>

<div class="container mt-4">
    <h2>Manage Modules</h2>
    <?= $message ?>
</div>

<div class="container mt-3">
    <form method="GET" class="d-flex gap-3">
        <select name="filter" class="form-select" style="max-width:300px">
            <option value="all"  <?= $filter=='all'?'selected':'' ?>>All Subjects</option>
            <option value="comp" <?= $filter=='comp'?'selected':'' ?>>Comp</option>
            <option value="busi" <?= $filter=='busi'?'selected':'' ?>>Busi</option>
            <option value="desi" <?= $filter=='desi'?'selected':'' ?>>Desi</option>
        </select>
        <button class="btn btn-custom">Filter</button>
    </form>
</div>

<div class="container mt-4">
    <form method="POST" class="bg-secondary p-4 rounded">
        <?php if ($editData): ?>
            <h4>Edit Module</h4>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="moduleID" value="<?= $editData['moduleID'] ?>">
        <?php else: ?>
            <h4>Add New Module</h4>
            <input type="hidden" name="action" value="add">
        <?php endif; ?>

        <label class="form-label mt-3">Module Code</label>
        <input class="form-control" name="modulecode" required 
               value="<?= $editData['modulecode'] ?? '' ?>">

        <label class="form-label mt-3">Module Name</label>
        <textarea class="form-control" name="modulename" rows="2" required>
<?= $editData['modulename'] ?? '' ?></textarea>

        <label class="form-label mt-3">Subject</label>
        <select class="form-select" name="subject">
            <option value="comp" <?= isset($editData) && $editData['subject']=='comp'?'selected':'' ?>>Comp</option>
            <option value="busi" <?= isset($editData) && $editData['subject']=='busi'?'selected':'' ?>>Busi</option>
            <option value="desi" <?= isset($editData) && $editData['subject']=='desi'?'selected':'' ?>>Desi</option>
        </select>

        <button class="btn btn-custom mt-3" type="submit">Save</button>
    </form>
</div>

<div class="container mt-5">
    <h4>Module List</h4>

    <table class="table table-dark table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Module Code</th>
                <th>Module Name</th>
                <th>Subject</th>
                <th width="160px">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($modules as $m): ?>
            <tr>
                <td><?= $m['moduleID'] ?></td>
                <td><?= htmlspecialchars($m['modulecode']) ?></td>
                <td><?= htmlspecialchars($m['modulename']) ?></td>
                <td><?= strtoupper($m['subject']) ?></td>
                <td>
                    <a href="?edit=<?= $m['moduleID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?delete=<?= $m['moduleID'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this module?')">
                        Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>

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

if (isset($_GET['PostID'])) {
    $postID = (int)$_GET['PostID'];
} elseif (isset($_GET['postID'])) {
    $postID = (int)$_GET['postID'];
} else {
    header("Location: manage_posts.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']);
    $content  = trim($_POST['content']); 
    $moduleID = $_POST['moduleID'];     
    $imagePath = $_POST['current_image']; 
    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetDir = "../upload/"; 

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = "upload/" . $fileName;
        }
    }
    $update = $pdo->prepare("
        UPDATE posts 
        SET title = :title, 
            content = :content,
            moduleID = :moduleID, 
            image = :image 
        WHERE postID = :id
    ");

    $update->execute([
        ':title'    => $title,
        ':content'  => $content,
        ':moduleID' => $moduleID,
        ':image'    => $imagePath,
        ':id'       => $postID
    ]);

    header("Location: manage_posts.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.*, m.subject, m.modulecode 
    FROM posts p
    JOIN modules m ON p.moduleID = m.moduleID
    WHERE p.postID = :id
");
$stmt->execute([':id' => $postID]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("Post not found!");
}

$stmtMods = $pdo->query("SELECT moduleID, modulename, subject, modulecode FROM modules ORDER BY subject, modulename ASC");
$allModules = $stmtMods->fetchAll(PDO::FETCH_ASSOC);

$subjects = array_unique(array_column($allModules, 'subject'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post (Admin)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">

<div class="container mt-5 mb-5">
    <h1 class="mb-4 text-warning">Edit Post</h1>

    <form action="" method="POST" enctype="multipart/form-data" class="bg-secondary p-4 rounded shadow">

        <div class="mb-3">
            <label class="form-label fw-bold">Title:</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>

        <div class="mb-3 p-3 bg-dark rounded border border-light">
            <label class="form-label text-warning fw-bold">Filter by Subject:</label><br>
            
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="subject_filter" id="f_all" value="ALL" onchange="filterModules(this.value)">
                <label class="form-check-label" for="f_all">Show All</label>
            </div>

            <?php foreach ($subjects as $idx => $subj): ?>
                <?php $checked = ($subj == $post['subject']) ? 'checked' : ''; ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" 
                           name="subject_filter" 
                           id="f_<?= $idx ?>" 
                           value="<?= htmlspecialchars($subj) ?>" 
                           <?= $checked ?>
                           onchange="filterModules(this.value)">
                    <label class="form-check-label" for="f_<?= $idx ?>"><?= htmlspecialchars($subj) ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Module Code:</label>
            <select name="moduleID" id="moduleSelect" class="form-select" required>
                <?php foreach ($allModules as $mod): ?>
                    <?php $selected = ($mod['moduleID'] == $post['moduleID']) ? 'selected' : ''; ?>
                    <option value="<?= $mod['moduleID'] ?>" 
                            data-subject="<?= htmlspecialchars($mod['subject']) ?>"
                            <?= $selected ?>>
                        <?= htmlspecialchars($mod['modulecode']) ?> - <?= htmlspecialchars($mod['modulename']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Content:</label>
            <textarea name="content" class="form-control" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Current Image:</label><br>
            <?php if (!empty($post['image'])): ?>
                <img src="../<?= htmlspecialchars($post['image']) ?>" width="150" class="img-thumbnail bg-dark border-secondary">
            <?php else: ?>
                <span class="text-warning">No image uploaded</span>
            <?php endif; ?>
            
            <input type="hidden" name="current_image" value="<?= htmlspecialchars($post['image']) ?>">
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Upload New Image (optional):</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-warning fw-bold px-4">Update Post</button>
        <a href="manage_posts.php" class="btn btn-light px-4">Cancel</a>
    </form>
</div>

<script>
function filterModules(selectedSubject) {
    const selectBox = document.getElementById('moduleSelect');
    const options = selectBox.getElementsByTagName('option');

    for (let i = 0; i < options.length; i++) {
        let option = options[i];
        let moduleSubject = option.getAttribute('data-subject');

        if (selectedSubject === 'ALL' || moduleSubject === selectedSubject) {
            option.style.display = 'block';
            option.disabled = false;
        } else {
            option.style.display = 'none';
            option.disabled = true;
        }
    }
}
document.addEventListener("DOMContentLoaded", function() {
    const checkedRadio = document.querySelector('input[name="subject_filter"]:checked');
    if (checkedRadio) {
        filterModules(checkedRadio.value);
    }
});
</script>

</body>
</html>
<?php
session_start();
include 'config.php';

if (!isset($_SESSION['userID'])) {
    header("Location: templates/login.html.php");
    exit;
}
$userID = $_SESSION['userID'];

if (!isset($_GET['postID'])) {
    die("Error: Missing Post ID");
}
$postID = (int)$_GET['postID'];

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title    = trim($_POST['title']);
        $content  = trim($_POST['content']);
        $moduleID = $_POST['moduleID'];   
        $imagePath = $_POST['current_image'];

        if (!empty($_FILES['image']['name'])) {
            $fileName = time() . "_" . basename($_FILES['image']['name']);
            $targetDir = "upload/";        
            $targetFile = $targetDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            }
        }

        $stmtUpdate = $pdo->prepare("
            UPDATE posts 
            SET title = :title, content = :content, moduleID = :moduleID, image = :image 
            WHERE postID = :id AND userID = :uid
        ");
        
        $stmtUpdate->execute([
            ':title'    => $title,
            ':content'  => $content,
            ':moduleID' => $moduleID,
            ':image'    => $imagePath,
            ':id'       => $postID,
            ':uid'      => $userID
        ]);

        header("Location: my_posts.php?msg=updated");
        exit;
    }

    $stmtPost = $pdo->prepare("
        SELECT p.*, m.subject, m.moduleID as currentModuleID 
        FROM posts p
        JOIN modules m ON p.moduleID = m.moduleID
        WHERE p.postID = :id AND p.userID = :uid
    ");
    $stmtPost->execute([':id' => $postID, ':uid' => $userID]);
    $post = $stmtPost->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("The post does not exist or you do not have permission to edit it.");
    }

    $stmtMods = $pdo->query("SELECT moduleID, modulename, subject, modulecode FROM modules ORDER BY subject, modulename ASC");
    $allModules = $stmtMods->fetchAll(PDO::FETCH_ASSOC);
    $subjects = array_unique(array_column($allModules, 'subject'));

} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>

<?php include "templates/header.html.php"; ?>

<nav class="underline-nav" style="background-color: #2E3D54;">  
    <a href="my_posts.php">←</a>   
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="bi bi-pencil-square"></i> Editing Post: #<?= $postID ?>
                </div>
                <div class="card-body p-4">

                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Title</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
                        </div>

                        <div class="mb-3 p-3 bg-light rounded border">
                            <label class="form-label fw-bold mb-2">Filter Category:</label><br>
                            
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
                            <label class="form-label fw-bold">Select Module</label>
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
                            <label class="form-label fw-bold">Content</label>
                            <textarea name="content" class="form-control" rows="8" required><?= htmlspecialchars($post['content']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Image</label>
                            
                            <?php if (!empty($post['image'])): ?>
                                <div class="mb-2">
                                    <img src="<?= htmlspecialchars($post['image']) ?>" class="img-thumbnail" style="height: 150px;">
                                    <div class="form-text">Current image</div>
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" name="image" class="form-control">
                            <input type="hidden" name="current_image" value="<?= htmlspecialchars($post['image']) ?>">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning fw-bold">Update Post</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<?php
    include "templates/footer.html.php";
?>
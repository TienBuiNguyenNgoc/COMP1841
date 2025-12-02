<?php
session_start();
include '../config.php';

if (!isset($_SESSION['userID'])) {
    header("Location: login.html.php");
    exit;
}

$pdo = new PDO($dsn, $user, $pass);
$userid = (int)$_SESSION['userID'];
$message = "";
$stmtModules = $pdo->query("SELECT moduleID, modulename, subject FROM modules ORDER BY subject, modulename ASC");
$modules = $stmtModules->fetchAll(PDO::FETCH_ASSOC);
$subjects = array_unique(array_column($modules, 'subject'));
$subjects = array_filter($subjects);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']);
    $content  = trim($_POST['content']);
    $moduleID = trim($_POST['moduleID']); 

    if (empty($title) || empty($content) || empty($moduleID)) {
        $message = "<p style='color:red;'>Please fill in all the information.</p>";
    } else {
        $imagePath = null; 

        if (!empty($_FILES['image']['name'])) {
            $uploadDir = "../upload/"; 
            $fileName = time() . "_" . basename($_FILES['image']['name']);
            $fileSystemPath = $uploadDir . $fileName; 
            $databasePath = "upload/" . $fileName;
            $allowed = ['jpg','jpeg','png','gif'];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $message = "<p style='color:red;'>Only JPG, PNG, and GIF files are allowed.</p>";
            } else {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $fileSystemPath)) {
                    $imagePath = $databasePath; 
                } else {
                    $message = "<p style='color:red;'>Cannot upload image (Folder Permission error or file too large).</p>";
                }
            }
        }

        if ($message === "") {
            $stmt = $pdo->prepare("
                INSERT INTO posts (userID, title, moduleID, content, image, created)
                VALUES (:userid, :title, :moduleID, :content, :image, NOW())
            ");
            $stmt->execute([
                ':userid'   => $userid,
                ':title'    => $title,
                ':moduleID' => $moduleID,
                ':content'  => $content,
                ':image'    => $imagePath
            ]);
            header("Location: ../index.php?post=success");
            exit;
        }
    }
}
?>

<?php include "header.html.php"; ?>

<nav class="underline-nav" style="background-color: #2E3D54;">  
    <a href="../dashboard.php">←</a>   
    <a href="../index.php">Home</a>
    <a href="../contact/contact.html.php">Contact</a>
</nav>

<div class="container p-4 rounded shadow-sm">
    <h2 class="mb-4 fw-bold text-center">Create New Post</h2>

    <?= $message ?>

    <form action="" method="post" enctype="multipart/form-data">

        <div class="mb-3 p-3 rounded border">
            <label class="form-label fw-bold">Filter Modules by Subject:</label>
            
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="subject_filter" id="subj_all" value="ALL" checked onchange="filterModules(this.value)">
                <label class="form-check-label" for="subj_all">All</label>
            </div>

            <?php foreach ($subjects as $index => $subj): ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" 
                           name="subject_filter" 
                           id="subj_<?= $index ?>" 
                           value="<?= htmlspecialchars($subj) ?>" 
                           onchange="filterModules(this.value)">
                    <label class="form-check-label" for="subj_<?= $index ?>">
                        <?= htmlspecialchars($subj) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <label class="form-label fw-bold">Module:</label><br>
        <select name="moduleID" id="moduleSelect" class="form-select" required>
            <option value="">-- Select Module --</option>
            
            <?php foreach ($modules as $mod): ?>
                <option value="<?= $mod['moduleID'] ?>" data-subject="<?= htmlspecialchars($mod['subject']) ?>">
                    <?= htmlspecialchars($mod['modulename']) ?> 
                    (<?= htmlspecialchars($mod['subject']) ?>) </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label class="form-label fw-bold">Title:</label><br>
        <input type="text" name="title" class="form-control" required><br>

        <label class="form-label fw-bold">Content:</label><br>
        <textarea name="content" class="form-control" rows="5" required></textarea><br>

        <label class="form-label fw-bold">Upload Image:</label><br>
        <input type="file" name="image" class="form-control"><br>

        <button type="submit" class="btn fw-bold text-white" style="background-color: #FF004D;">Create Post</button>
    </form>
</div>

<script>
function filterModules(selectedSubject) {
    const selectBox = document.getElementById('moduleSelect');
    const options = selectBox.getElementsByTagName('option');

    selectBox.value = "";

    for (let i = 0; i < options.length; i++) {
        let option = options[i];
        
        if (option.value === "") {
            continue; 
        }

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
</script>
</body>
</html>

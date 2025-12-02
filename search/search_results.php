<?php
include '../config.php'; 

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $q = trim($_GET['q'] ?? '');
    $posts = [];

    if (!empty($q)) {
        $keywords = explode(' ', $q);
        $where = [];
        $params = [];

        $sql = "SELECT p.*, m.subject, m.modulecode, m.modulename 
                FROM posts p
                JOIN modules m ON p.moduleID = m.moduleID";

        foreach ($keywords as $word) {
            $where[] = "(p.title LIKE ? OR m.modulecode LIKE ? OR m.modulename LIKE ? OR m.subject LIKE ?)";
            $params[] = "%$word%";
            $params[] = "%$word%";
            $params[] = "%$word%";
            $params[] = "%$word%";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY p.created DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>

<?php include "../templates/header.html.php"; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<nav class="underline-nav" style="background-color: #2E3D54;">  
    <a href="../dashboard.php">← Dashboard</a>   
    <a href="../index.php">Home</a>
    <a href="../contact/contact.html.php">Contact</a>
</nav>

<div class="container py-5">
    <h2 class="text-center mb-4">Search Results for “<span class="text-primary"><?= htmlspecialchars($q) ?></span>”</h2>

    <?php if (count($posts) > 0): ?>
        <div class="row g-4">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="../view_post.php?postID=<?= $post['postID'] ?>" class="text-decoration-none text-dark">
                        <div class="card shadow-sm h-100 hover-effect">
                            
                            <?php 
                                $subjName = !empty($post['subject']) ? strtoupper($post['subject']) : 'DEFAULT';
                            ?>
                            <img src="../img_bg/<?= htmlspecialchars($subjName) ?>.jpg" 
                                 class="card-img-top" 
                                 alt="Post image"
                                 style="height: 200px; object-fit: cover;"
                                 onerror="this.src='../img_bg/DEFAULT.jpg';">

                            <div class="card-body">
                                <h5 class="card-title fw-bold text-truncate"><?= htmlspecialchars($post['title']) ?></h5>
                                
                                <p class="card-text text-secondary">
                                    <?= nl2br(htmlspecialchars(substr($post['content'], 0, 80))) ?>...
                                </p>

                                <hr>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-book"></i> <?= htmlspecialchars($post['modulecode']) ?>
                                    </small>
                                    <span class="badge bg-info text-dark">
                                        <?= htmlspecialchars($post['subject']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="alert alert-warning text-center mt-4 p-5">
            <h4>No posts found matching "<?= htmlspecialchars($q) ?>"</h4>
            <p>Try searching for module code (e.g., COMP1770) or keywords in the title.</p>
            <a href="../index.php" class="btn btn-outline-dark mt-2">Back to Home</a>
        </div>
    <?php endif; ?>
</div>

<style>
.hover-effect {
    transition: transform 0.2s;
}
.hover-effect:hover {
    transform: translateY(-5px);
}
</style>

</body>
</html>
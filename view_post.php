<?php
session_start();
include 'config.php';
if (!isset($_GET['postID']) || empty($_GET['postID'])) {
    die("Error: Post ID is missing.");
}

$postID = (int)$_GET['postID'];
$currentUserID = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : 0;

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT posts.*, modules.modulename, modules.subject, modules.modulecode, users.username as authorName FROM posts 
            JOIN modules ON posts.moduleID = modules.moduleID 
            LEFT JOIN users ON posts.userID = users.userID WHERE posts.postID = :postID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':postID' => $postID]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("Post not found!");
    }

    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE postID = :postID");
    $stmtCount->execute([':postID' => $postID]);
    $totalLikes = $stmtCount->fetchColumn();

    $isLiked = false;
    if ($currentUserID > 0) {
        $stmtCheck = $pdo->prepare("SELECT likeID FROM likes WHERE postID = :postID AND userID = :userID");
        $stmtCheck->execute([':postID' => $postID, ':userID' => $currentUserID]);
        if ($stmtCheck->fetch()) {
            $isLiked = true;
        }
    }
    $sqlReplies = "SELECT replies.*, users.username FROM replies 
                   JOIN users ON replies.userID = users.userID 
                   WHERE replies.postID = :postID 
                   ORDER BY replies.created DESC";
    $stmtReplies = $pdo->prepare($sqlReplies);
    $stmtReplies->execute([':postID' => $postID]);
    $replies = $stmtReplies->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>

<?php include "templates/header.html.php"; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<nav class="underline-nav" style="background-color: #2E3D54;">  
    <a href="dashboard.php">←</a>
    <a href="index.php"> Home</a>   
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php 
                $subjName = !empty($post['subject']) ? strtoupper($post['subject']) : 'DEFAULT';
            ?>

            <h1 class="fw-bold mb-3"><?= htmlspecialchars($post['title']) ?></h1>
            
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <span>
                    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($post['authorName'] ?? 'Unknown') ?> | 
                    <i class="bi bi-book"></i> <?= htmlspecialchars($post['modulecode']) ?>
                </span>
                
                <button id="likeBtn" 
                        class="btn <?= $isLiked ? 'btn-danger' : 'btn-outline-danger' ?> rounded-pill" onclick="toggleLike(<?= $postID ?>)">
                    <i id="likeIcon" class="bi <?= $isLiked ? 'bi-heart-fill' : 'bi-heart' ?>"></i> 
                    <span id="likeCount" class="fw-bold"><?= $totalLikes ?></span> Likes
                </button>
            </div>

            <div class="content fs-5 mb-5" style="line-height: 1.8;">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
            
            <?php if (!empty($post['image'])): ?>
                <div class="mb-5">
                    <img src="<?= htmlspecialchars($post['image']) ?>" class="img-fluid rounded border" alt="User Image">
                </div>
            <?php endif; ?>

            <hr>

            <div class="p-4 rounded-3 shadow-sm">

                <h4 class="fw-bold mb-4"><i class="bi bi-chat-dots"></i> Replies (<?= count($replies) ?>)</h4>

                <?php if ($currentUserID > 0): ?>
                    <form action="reply.php" method="POST" class="mb-5">
                        <input type="hidden" name="postID" value="<?= $postID ?>">
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="3" placeholder="Write your reply here..." required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-send"></i> Post Reply</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        Please <a href="templates/login.html.php" class="fw-bold">login</a> to join the discussion.
                    </div>
                <?php endif; ?>

                <div class="replies-list">
                    <?php if (count($replies) > 0): ?>
                        <?php foreach ($replies as $rep): ?>
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-person-circle fs-1 text-secondary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="fw-bold text-primary mb-0">
                                                    <?= htmlspecialchars($rep['username']) ?>
                                                </h6>
                                                <small style="font-size: 0.8rem;">
                                                    <?= date("d M Y, H:i", strtotime($rep['created'])) ?>
                                                </small>
                                            </div>
                                            
                                            <p class="mb-0 text-dark" style="white-space: pre-line;">
                                                <?= htmlspecialchars($rep['content']) ?>
                                            </p>

                                            <?php if ($currentUserID > 0 && $currentUserID == $rep['userID']): ?>
                                                <div class="mt-2 border-top pt-2 d-flex justify-content-end gap-2">
                                                    <a href="edit_reply.php?replyID=<?= $rep['replyID'] ?>" 
                                                       class="btn btn-sm btn-link text-decoration-none text-warning p-0">
                                                       <i class="bi bi-pencil-square"></i> Edit
                                                    </a>
                                                    
                                                    <a href="delete_reply.php?replyID=<?= $rep['replyID'] ?>" 
                                                       class="btn btn-sm btn-link text-decoration-none text-danger p-0 ms-2"
                                                       onclick="return confirm('Are you sure you want to delete this comment?')">
                                                       <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted fst-italic py-3">No replies yet. Be the first!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const isLoggedIn = <?= $currentUserID > 0 ? 'true' : 'false' ?>;
function toggleLike(postID) {
    if (!isLoggedIn) {
        alert("Please login to like this post!"); return;
    }
    fetch('likes.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ postID: postID })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('likeCount').innerText = data.new_count;
            const icon = document.getElementById('likeIcon');
            const btn = document.getElementById('likeBtn');
            if (data.action === 'liked') {
                icon.className = 'bi bi-heart-fill';
                btn.className = 'btn btn-danger rounded-pill';
            } else {
                icon.className = 'bi bi-heart';
                btn.className = 'btn btn-outline-danger rounded-pill';
            }
        } else {
            alert(data.message);
        }
    })
    .catch(err => console.error(err));
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
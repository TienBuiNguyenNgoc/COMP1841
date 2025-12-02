<?php
    include 'templates/header.html.php';
?>

    <div class="container">
      <nav class="underline-nav" style="justify-content: center; align-items: center; background-color: #2E3D54;">  
        <a href="dashboard.php">←</a>   
        <a href="index.php">Home</a>
        <a href="contact/contact.html.php">Contact</a> 
        <form class="search-bar" action="./search/search_results.php" method="get"> 
        <input type="text" name="q" placeholder="Search the subject" aria-label="Search">
        <button type="submit"><i class="bi bi-search"></i></button>
        </button> 
      </form>
        <?php 
            include "login_state.php";
        ?>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
      </nav>
      <br>
      <div class="section-header">
        <div class="category-title">Dashboard</div>
      </div>

<?php
include './config.php';
$pdo = new PDO($dsn, $user, $pass);

$subject = $_GET['subject'] ?? 'comp'; 
$stmt = $pdo->prepare("SELECT Modulecode, Modulename, Subject FROM modules WHERE Subject = ?");
$stmt->execute([$subject]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <?php if (count($modules) > 0): ?>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($modules as $module): ?>
                <div class="col">
                    <div class="card movie-card">
                        <img src="img_bg/<?= strtoupper($module['Subject']) ?>.jpg" class="card-img-top" alt="Module Image">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($module['Modulecode']) ?></h5>
                            <p class="card-subtitle" style="color: #fff;">
                                <i class="bi bi-book"></i> <?= htmlspecialchars($module['Modulename']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No modules found for this subject.</p>
    <?php endif; ?>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

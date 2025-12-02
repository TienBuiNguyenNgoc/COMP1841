<?php

include './config.php'; 
$pdo = new PDO($dsn, $user, $pass);

$subject = isset($_GET['subject']) ? strtoupper($_GET['subject']) : 'COMP';

$stmt = $pdo->prepare("SELECT * FROM modules WHERE modulecode LIKE :subject ORDER BY RAND() LIMIT 6");
$stmt->execute(['subject' => "$subject%"]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->prepare("SELECT * FROM modules WHERE modulecode LIKE :subject ORDER BY RAND() LIMIT 2");
$stmt2->execute(['subject' => "$subject%"]);
$topics = $stmt2->fetchAll(PDO::FETCH_ASSOC);

?>
  <div class="container">
   <nav class="underline-nav" style="justify-content: center; align-items: center; background-color: #2E3D54;"> 
      <a href="index.php">Home</a>  
      <a href="contact/contact.html.php">Contact</a>  
      <form class="search-bar" action="./search/search_results.php" method="get"> 
        <input type="text" name="q" placeholder="Search the subject" aria-label="Search">
        <button type="submit"><i class="bi bi-search"></i></button>
        </button> 
      </form> 
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
    </nav>

    <br>

    <div class="section-header">
      <div class="category-title">COMP</div>
      <a href="BSc_computing.php" class="view-all" style="color: #fff;">VIEW ALL <i class="bi bi-caret-right-fill"></i></a>
    </div>

    <div class="subheading" style="color: #fff;">Popular subjects in COMP</div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-4">
      <?php foreach ($modules as $module): ?>
        <div class="col">
          <div class="card movie-card">
            <img src="img_bg/COMP.jpg" class="card-img-top" alt="<?= htmlspecialchars($module['modulename']) ?>">
            <div class="card-body p-0">
              <h5 class="card-title"><?= htmlspecialchars($module['modulename']) ?></h5>
              <p class="card-subtitle"><i class="bi bi-book"></i> <?= htmlspecialchars($module['modulecode']) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="container py-5">
    <h4 class="section-title">Top topics</h4>

    <div class="row g-4">
      <?php foreach ($topics as $topic): ?>
        <div class="col-md-6">
          <div class="card news-card">
            <div class="position-relative">
              <img src="img_bg/COMP.jpg" class="card-img-top" alt="<?= htmlspecialchars($topic['modulename']) ?>">
              <span class="badge badge-gs"><?= htmlspecialchars($topic['modulecode']) ?></span>
            </div>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($topic['modulename']) ?></h5>
              <?php if (!empty($topic['description'])): ?>
                <p class="card-text text-muted"><?= htmlspecialchars($topic['description']) ?></p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

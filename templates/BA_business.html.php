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

        $stmt = $pdo->prepare("SELECT Modulecode, Modulename, Subject FROM modules WHERE Subject = ?");
        $stmt->execute(['busi']);
        $busis = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="container mt-4">
            <?php if (count($busis) > 0): ?>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                    <?php foreach ($busis as $busi): ?>
                        <div class="col">
                            <div class="card movie-card shadow-sm">
                                <img src="img_bg/busi.jpg" 
                                    class="card-img-top" 
                                    alt="<?= htmlspecialchars($busi['Subject']) ?> Module Image">

                                <div class="card-body text-center">
                                    <h5 class="card-title mb-2"><?= htmlspecialchars($busi['Modulecode']) ?></h5>
                                    <p class="card-subtitle" style="color: #fff;">
                                        <i class="bi bi-book"></i> <?= htmlspecialchars($busi['Modulename']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-muted mt-3">No BUSI modules found.</p>
            <?php endif; ?>
        </div>
        <script src="img_bg/busi.jpg"></script>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php
    include "templates/footer.html.php";
?>
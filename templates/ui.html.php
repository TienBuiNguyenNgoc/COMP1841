<nav class="underline-nav" style="justify-content: center; align-items: center; background-color: #2E3D54;"> 
      <img src="img_bg/Logo.png" alt="Bootstrap" width="200" height="40">
      <a href="contact/contact.html.php">Contact</a> 
    <form class="search-bar" id="searchForm" action="search/search_results.php" method="GET">
      <input 
        type="text" 
        name="q" 
        id="searchInput" 
        placeholder="Search by subject, module code, or title..." 
        aria-label="Search" 
        autocomplete="off"
        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
      >
      <button type="submit">
        <i class="bi bi-search"></i>
      </button>

      <div id="suggestions" class="suggestions-box"></div>
    </form>

    <?php 
      include "login_state.php";
    ?>
</nav>
    
<img src="img_bg/Logo.png" class="img-fluid d-block mx-auto" alt="center image" width="200" height="50">
<p style="text-align: center; color: #fff; font-size: 24px;">Guarding the shores of knowledge.</p>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container-fluid py-4">
  <div> 
    <?php
      include "config.php";
      try {
          $pdo = new PDO($dsn, $user, $pass);
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          $sql = "SELECT posts.*, modules.subject, modules.modulecode 
                  FROM posts 
                  JOIN modules ON posts.moduleID = modules.moduleID 
                  ORDER BY RAND() 
                  LIMIT 5";
          $stmt = $pdo->query($sql);
          $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

          function getDocsBySubject($pdo, $subjectName) {
              $sql = "SELECT modulecode FROM modules WHERE subject = :sub ORDER BY RAND() LIMIT 3";
              $stmt = $pdo->prepare($sql);
              $stmt->execute([':sub' => $subjectName]);
              return $stmt->fetchAll(PDO::FETCH_ASSOC);
          }

          $computingList = getDocsBySubject($pdo, 'comp'); 
          $designList    = getDocsBySubject($pdo, 'desi');
          $businessList  = getDocsBySubject($pdo, 'busi');

      } catch (PDOException $e) {
          echo "DB connection failed: " . $e->getMessage();
          $posts = []; 
          $computingList = []; $designList = []; $businessList = [];
      }
    ?>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-4"> <?php if (!empty($posts)): ?>
      <?php foreach ($posts as $post): ?>
      <div class="col">
        <a href="view_post.php?postID=<?= $post['postID'] ?>" class="text-decoration-none text-dark d-block mb-4">
        <div class="custom-card h-100 shadow-sm">
        <?php 
          $subjName = !empty($post['subject']) ? strtoupper($post['subject']) : 'DEFAULT';
        ?>
        <img src="img_bg/<?= htmlspecialchars($subjName) ?>.jpg" class="card-img-top" alt="Module Image"
        style="height: 200px; object-fit: cover;" onerror="this.src='img_bg/DEFAULT.jpg';">                               
          <div class="card-body text-center">
            <h5 class="card-title text-truncate"><?= htmlspecialchars($post['title']) ?></h5>
            <p class="card-subtitle text-muted"><i class="bi bi-book"></i><?= htmlspecialchars($post['modulecode'] ?? 'Unknown') ?></p>
          </div>
        </div></a>
      </div>
      <?php endforeach; ?>
      <?php else: ?>
        <p class="text-white text-center w-100">There are no posts to display yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="top-wikis-section">
  <div class="top-wikis-title"><i class="bi bi-book-half"></i><span>Hot Topics</span></div>
    <div class="wikis-columns">  
      <div class="wiki-category">
          <h6>COMPUTING</h6>
          <ul>
          <?php if (!empty($computingList)): ?>
          <?php foreach($computingList as $item): ?>
            <li>
              <a href="search/search_results.php?q=<?= htmlspecialchars($item['modulecode']) ?>"><?= htmlspecialchars($item['modulecode']) ?></a>
            </li>
          <?php endforeach; ?>
          <?php else: ?>
            <li><small>No data found</small></li>
          <?php endif; ?>
          </ul>
      </div>

      <div class="wiki-category">
        <h6>DESIGN</h6>
        <ul>
        <?php if (!empty($designList)): ?>
        <?php foreach($designList as $item): ?>
          <li>
            <a href="search/search_results.php?q=<?= htmlspecialchars($item['modulecode']) ?>"><?= htmlspecialchars($item['modulecode']) ?></a>
          </li>
        <?php endforeach; ?>
        <?php else: ?>
          <li><small>No data found</small></li>
        <?php endif; ?>
         </ul>
      </div>
          
      <div class="wiki-category">
        <h6>BUSINESS</h6>
        <ul>
        <?php if (!empty($businessList)): ?>
        <?php foreach($businessList as $item): ?>
          <li>
            <a href="search/search_results.php?q=<?= htmlspecialchars($item['modulecode']) ?>"><?= htmlspecialchars($item['modulecode']) ?></a>
          </li>
        <?php endforeach; ?>
        <?php else: ?>
            <li><small>No data found</small></li>
        <?php endif; ?>
        </ul>
      </div>
</div>
    
<div class="explore-more">
  <span style="color: #fff;">Plus over to explore more</span>
  <a href="dashboard.php"><button class="arrow-btn" ><i class="bi bi-arrow-right" ></i></button></a>
</div>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
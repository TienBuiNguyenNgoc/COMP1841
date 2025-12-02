<?php
  include "../templates/header.html.php";
?>

<div class="container">
    <nav class="underline-nav" style="justify-content: center; align-items: center; background-color: #0F1C2C;">
        <a href="../index.php">Home</a>
    </nav>
</div>
<div class="container p-5 rounded shadow-sm" style="background-color: #2E3D54;">
  <h3 class="mb-4 fw-bold text-center">Contact</h3>

  <form method="post" action="contact.php" enctype="multipart/form-data">
      
      <input type="hidden" name="to" value="boscortg@gmail.com">

      <label class="form-label fw-bold">Message:</label>
      <textarea id="postContent" name="message" class="form-control" rows="7" required></textarea>

      <label class="form-label fw-bold">Image</label><br>
      <input type="file" name="image" accept="image/*" class="form-control"><br>

      <button type="submit" class="btn w-10 fw-bold" style="background-color: #FF004D;">Send</button>
  </form>
</div>
</body>
</html>

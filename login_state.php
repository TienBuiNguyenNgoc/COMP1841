<?php
if (!empty($_SESSION['Username'])) {
    $safeFilename = !empty($_SESSION['Avatar']) ? basename($_SESSION['Avatar']) : '';
    $avatarFile = $safeFilename ? 'avatar/' . $safeFilename : 'avatar/default.png';

    if (!file_exists($avatarFile)) {
        $avatarFile = 'avatar/default.png';
    }

    $avatarEsc = htmlspecialchars($avatarFile, ENT_QUOTES, 'UTF-8');
    $usernameEsc = htmlspecialchars($_SESSION['Username'], ENT_QUOTES, 'UTF-8');
    $role = $_SESSION['Role'] ?? 'user';

    echo '
    <div class="dropdown user-dropdown">
      <a href="#" class="d-block" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="' . $avatarEsc . '" alt="User Avatar" class="avatar-img">
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
        <li><h6 class="dropdown-header" style="color: #fff">' . $usernameEsc . '</h6></li>';

    if ($role === 'admin') {
        echo '
      <li class="dropdown-submenu">
        <a class="dropdown-item" href="#">Manage</a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="admin/manage_posts.php">Manage Posts</a></li>
          <li><a class="dropdown-item" href="admin/manage_users.php">Manage Users</a></li>
          <li><a class="dropdown-item" href="admin/manage_module.php">Manage Module</a></li>
          <li><a class="dropdown-item" href="admin/manage_contact.php">Manage Contact</a></li>
        </ul>
      </li>';
    }

    echo '
        <li><a class="dropdown-item" href="templates/create_post.html.php">New post</a></li>
        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
      </ul>
    </div>';
} else {
    echo '<a href="./templates/login.html.php" class="login" role="button">Log in</a>';
}
?>

<?php
$q = trim($_GET['q'] ?? '');
$safe_q = urlencode($q);
header("Location: search_results.php?q=" . $safe_q);
exit;
?>
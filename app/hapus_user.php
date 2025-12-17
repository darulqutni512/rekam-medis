<?php
require_once __DIR__ . '/inc/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0; if ($id<=0){ header('Location: users.php'); exit; }
$stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id_user = ?"); mysqli_stmt_bind_param($stmt,'i',$id); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt); header('Location: users.php'); exit;
?>

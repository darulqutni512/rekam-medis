<?php
require_once __DIR__ . '/inc/db.php';
$errors=[]; $username=''; $nama=''; $email=''; $role='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $username=trim($_POST['username'] ?? ''); $nama=trim($_POST['nama'] ?? ''); $email=trim($_POST['email'] ?? ''); $role=trim($_POST['role'] ?? ''); $password=trim($_POST['password'] ?? '');
  if ($username===''||$password==='') $errors[]='Username dan password wajib diisi.';
  if (empty($errors)){
    $pass_hash = password_hash($password, PASSWORD_DEFAULT);
    // ensure users table exists (create if missing)
    $create_sql = "CREATE TABLE IF NOT EXISTS users (
      id_user INT UNSIGNED NOT NULL AUTO_INCREMENT,
      username VARCHAR(100) NOT NULL UNIQUE,
      password_hash VARCHAR(255) NOT NULL,
      role VARCHAR(50) DEFAULT 'staff',
      nama VARCHAR(255) DEFAULT NULL,
      email VARCHAR(150) DEFAULT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id_user)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    if (!mysqli_query($koneksi, $create_sql)) {
      $errors[] = 'Gagal membuat tabel users: '.mysqli_error($koneksi);
    } else {
      $stmt = mysqli_prepare($koneksi, "INSERT INTO users (username, nama, email, role, password_hash) VALUES (?, ?, ?, ?, ?)");
      if (!$stmt) { $errors[] = 'Gagal menyiapkan query: '.mysqli_error($koneksi); }
      else {
        mysqli_stmt_bind_param($stmt,'sssss',$username,$nama,$email,$role,$pass_hash);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); header('Location: users.php'); exit; }
        $errors[]='Gagal menyimpan: '.mysqli_error($koneksi);
      }
    }
  }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Tambah User</title>
<link rel="stylesheet" href="../style.css"></head><body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container"><div class="card"><h2>Tambah User</h2>
<?php if(!empty($errors)): ?><div class="card" style="background:#ffecec;color:#a94442;padding:12px;margin-bottom:12px;"><?=htmlspecialchars(implode('\n',$errors))?></div><?php endif; ?>
<form method="POST" class="form-box">
<label>Username</label><input type="text" name="username" value="<?=htmlspecialchars($username)?>" required>
<label>Nama</label><input type="text" name="nama" value="<?=htmlspecialchars($nama)?>">
<label>Email</label><input type="email" name="email" value="<?=htmlspecialchars($email)?>">
<label>Role</label><input type="text" name="role" value="<?=htmlspecialchars($role)?>">
<label>Password</label><input type="password" name="password" required>
<div class="actions mt-12"><button class="btn" type="submit">Simpan</button><a href="users.php" class="btn-secondary">Kembali</a></div>
</form></div></main>
<?php include __DIR__ . '/inc/footer.php'; ?></body></html>

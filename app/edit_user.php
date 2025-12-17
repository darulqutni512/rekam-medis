<?php
require_once __DIR__ . '/inc/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0; if ($id<=0){ header('Location: users.php'); exit; }
$stmt = mysqli_prepare($koneksi, "SELECT id_user, username, nama, email, role FROM users WHERE id_user=?"); mysqli_stmt_bind_param($stmt,'i',$id); mysqli_stmt_execute($stmt); $res = mysqli_stmt_get_result($stmt); $data = mysqli_fetch_assoc($res); mysqli_stmt_close($stmt);
if (!$data){ header('Location: users.php'); exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $username = trim($_POST['username'] ?? ''); $nama = trim($_POST['nama'] ?? ''); $email = trim($_POST['email'] ?? ''); $role = trim($_POST['role'] ?? ''); $password = trim($_POST['password'] ?? '');
  if ($username==='') $errors[]='Username wajib diisi.';
  if (empty($errors)){
    if ($password !== ''){
      $pass_hash = password_hash($password, PASSWORD_DEFAULT);
      $u = mysqli_prepare($koneksi, "UPDATE users SET username=?, nama=?, email=?, role=?, password_hash=? WHERE id_user=?");
      mysqli_stmt_bind_param($u,'sssssi',$username,$nama,$email,$role,$pass_hash,$id);
    } else {
      $u = mysqli_prepare($koneksi, "UPDATE users SET username=?, nama=?, email=?, role=? WHERE id_user=?");
      mysqli_stmt_bind_param($u,'ssssi',$username,$nama,$email,$role,$id);
    }
    if (mysqli_stmt_execute($u)) { mysqli_stmt_close($u); header('Location: users.php'); exit; }
    $errors[] = 'Gagal memperbarui: '.mysqli_error($koneksi);
  }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Edit User</title>
<link rel="stylesheet" href="../style.css"></head><body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container"><div class="card"><h2>Edit User</h2>
<?php if(!empty($errors)): ?><div class="card" style="background:#ffecec;color:#a94442;padding:12px;margin-bottom:12px;"><?=htmlspecialchars(implode('\n',$errors))?></div><?php endif; ?>
<form method="POST" class="form-box">
<label>Username</label><input type="text" name="username" value="<?=htmlspecialchars($_POST['username'] ?? $data['username'])?>" required>
<label>Nama</label><input type="text" name="nama" value="<?=htmlspecialchars($_POST['nama'] ?? $data['nama'])?>">
<label>Email</label><input type="email" name="email" value="<?=htmlspecialchars($_POST['email'] ?? $data['email'])?>">
<label>Role</label><input type="text" name="role" value="<?=htmlspecialchars($_POST['role'] ?? $data['role'])?>">
<label>Password (kosongkan jika tidak ingin mengubah)</label><input type="password" name="password">
<div class="actions mt-12"><button class="btn" type="submit">Simpan Perubahan</button><a href="users.php" class="btn-secondary">Kembali</a></div>
</form></div></main>
<?php include __DIR__ . '/inc/footer.php'; ?></body></html>

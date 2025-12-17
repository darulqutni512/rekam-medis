<?php
require_once __DIR__ . '/inc/db.php';
$errors = [];
$nama = $spes = $no_sip = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_dokter'] ?? '');
    $spes = trim($_POST['spesialisasi'] ?? '');
    $no_sip = trim($_POST['no_sip'] ?? '');
    if ($nama === '') $errors[] = 'Nama dokter wajib diisi.';
    if (empty($errors)) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO dokter (nama_dokter, spesialisasi, no_sip) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sss', $nama, $spes, $no_sip);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); header('Location: dokter.php'); exit; }
        $errors[] = 'Gagal menyimpan: ' . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Dokter - Rekam Medis (Rebuild)</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <h2>Tambah Data Dokter</h2>
    <?php if (!empty($errors)): ?><div class="card" style="background:#ffecec;color:#a94442;padding:12px;margin-bottom:12px;"><?=htmlspecialchars(implode('\n',$errors))?></div><?php endif; ?>
    <form method="POST" class="form-box">
      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label>Nama Dokter</label>
          <input type="text" name="nama_dokter" value="<?= htmlspecialchars($nama) ?>" required>
        </div>
        <div class="form-group" style="flex:1">
          <label>Spesialisasi</label>
          <input type="text" name="spesialisasi" value="<?= htmlspecialchars($spes) ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>No. SIP</label>
          <input type="text" name="no_sip" value="<?= htmlspecialchars($no_sip) ?>">
        </div>
      </div>
      <div class="actions mt-12">
        <button type="submit" class="btn">Simpan</button>
        <a href="dokter.php" class="btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
</body>
</html>

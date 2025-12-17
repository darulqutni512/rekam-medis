<?php
require_once __DIR__ . '/inc/db.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama_obat'] ?? '');
  $stok = (int)($_POST['stok'] ?? 0);
  $satuan = trim($_POST['satuan'] ?? '');
  $keterangan = trim($_POST['keterangan'] ?? '');
  if ($nama === '') $errors[] = 'Nama obat wajib diisi.';
  if (empty($errors)) {
    $i = mysqli_prepare($koneksi, "INSERT INTO obat (nama_obat, stok, satuan, keterangan) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($i, 'siss', $nama, $stok, $satuan, $keterangan);
    if (mysqli_stmt_execute($i)) { mysqli_stmt_close($i); header('Location: obat.php'); exit; }
    $errors[] = 'Gagal menyimpan: ' . mysqli_error($koneksi);
  }
}
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Tambah Obat</title>
<link rel="stylesheet" href="../style.css"></head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <h2>Tambah Obat</h2>
    <?php if(!empty($errors)): ?><div class="card" style="background:#ffecec;color:#a94442;padding:12px;margin-bottom:12px;"><?php echo htmlspecialchars(implode("\n", $errors)); ?></div><?php endif; ?>
    <form method="POST" class="form-box">
      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label>Nama Obat</label>
          <input type="text" name="nama_obat" value="<?= htmlspecialchars($_POST['nama_obat'] ?? '') ?>" required>
        </div>
        <div class="form-group" style="flex:0 0 140px">
          <label>Stok</label>
          <input type="number" name="stok" value="<?= (int)($_POST['stok'] ?? 0) ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Satuan</label>
          <input type="text" name="satuan" value="<?= htmlspecialchars($_POST['satuan'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Keterangan</label>
          <textarea name="keterangan"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
        </div>
      </div>
      <div class="actions mt-12">
        <button class="btn" type="submit">Simpan Obat</button>
        <a href="obat.php" class="btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
</body></html>

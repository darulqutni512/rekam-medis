<?php
require_once __DIR__ . '/inc/db.php';

// Inisialisasi variabel
$nama_pasien = $nik = $tanggal_lahir = $jenis_kelamin = $alamat = $no_telepon = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pasien   = trim($_POST['nama_pasien'] ?? '');
    $nik           = trim($_POST['nik'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
    $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
    $alamat        = trim($_POST['alamat'] ?? '');
    $no_telepon    = trim($_POST['no_telepon'] ?? '');

    if ($nama_pasien === '' || $nik === '') $errors[] = 'Nama dan NIK wajib diisi.';

    if (empty($errors)) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO pasien (nama_pasien, nik, tanggal_lahir, jenis_kelamin, alamat, no_telepon) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssssss', $nama_pasien, $nik, $tanggal_lahir, $jenis_kelamin, $alamat, $no_telepon);
        $ok = mysqli_stmt_execute($stmt);
        if ($ok) {
            header('Location: pasien.php'); exit;
        } else {
            $errors[] = 'Gagal menyimpan: ' . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Pasien - Rekam Medis (Rebuild)</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <h2>Tambah Data Pasien</h2>
    <?php if (!empty($errors)): ?>
      <div class="card" style="background:#ffecec;color:#a94442;padding:12px;margin-bottom:12px;">
        <?= htmlspecialchars(implode('\n', $errors)) ?>
      </div>
    <?php endif; ?>
    <form method="POST" class="form-box">
      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label>Nama Pasien</label>
          <input type="text" name="nama_pasien" value="<?= htmlspecialchars($nama_pasien) ?>" required>
        </div>
        <div class="form-group" style="flex:1">
          <label>NIK</label>
          <input type="text" name="nik" value="<?= htmlspecialchars($nik) ?>" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Tanggal Lahir</label>
          <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($tanggal_lahir) ?>" required>
        </div>
        <div class="form-group">
          <label>Jenis Kelamin</label>
          <select name="jenis_kelamin" required>
            <option value="">-- Pilih --</option>
            <option value="Laki-laki" <?= $jenis_kelamin=='Laki-laki'?'selected':'' ?>>Laki-laki</option>
            <option value="Perempuan" <?= $jenis_kelamin=='Perempuan'?'selected':'' ?>>Perempuan</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label>Alamat</label>
          <textarea name="alamat"><?= htmlspecialchars($alamat) ?></textarea>
        </div>
        <div class="form-group" style="flex:0 0 220px">
          <label>No. Telepon</label>
          <input type="text" name="no_telepon" value="<?= htmlspecialchars($no_telepon) ?>">
        </div>
      </div>
      <div class="actions mt-12">
        <button type="submit" class="btn">Simpan</button>
        <a href="pasien.php" class="btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
</body>
</html>

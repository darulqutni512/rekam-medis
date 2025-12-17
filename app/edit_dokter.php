<?php
require_once __DIR__ . '/inc/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: dokter.php'); exit; }
$stmt = mysqli_prepare($koneksi, "SELECT id_dokter, nama_dokter, spesialisasi, no_sip FROM dokter WHERE id_dokter = ?");
mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt); $data = mysqli_fetch_assoc($res); mysqli_stmt_close($stmt);
if (!$data) { header('Location: dokter.php'); exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_dokter'] ?? '');
    $spes = trim($_POST['spesialisasi'] ?? '');
    $no_sip = trim($_POST['no_sip'] ?? '');
    if ($nama === '') $errors[] = 'Nama dokter wajib diisi.';
    if (empty($errors)) {
        $u = mysqli_prepare($koneksi, "UPDATE dokter SET nama_dokter=?, spesialisasi=?, no_sip=? WHERE id_dokter=?");
        mysqli_stmt_bind_param($u, 'sssi', $nama, $spes, $no_sip, $id);
        if (mysqli_stmt_execute($u)) { mysqli_stmt_close($u); header('Location: dokter.php'); exit; }
        $errors[] = 'Gagal memperbarui: '.mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Edit Dokter - Rekam Medis (Rebuild)</title>
<link rel="stylesheet" href="../style.css"></head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <h2>Edit Data Dokter</h2>
    <?php if (!empty($errors)): ?><div class="card" style="background:#ffecec;color:#a94442;padding:12px;margin-bottom:12px;"><?=htmlspecialchars(implode('\n',$errors))?></div><?php endif; ?>
    <form method="POST" class="form-box">
      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label>Nama Dokter</label>
          <input type="text" name="nama_dokter" value="<?= htmlspecialchars($_POST['nama_dokter'] ?? $data['nama_dokter']) ?>" required>
        </div>
        <div class="form-group" style="flex:1">
          <label>Spesialisasi</label>
          <input type="text" name="spesialisasi" value="<?= htmlspecialchars($_POST['spesialisasi'] ?? $data['spesialisasi']) ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>No. SIP</label>
          <input type="text" name="no_sip" value="<?= htmlspecialchars($_POST['no_sip'] ?? $data['no_sip']) ?>">
        </div>
      </div>
      <div class="actions mt-12">
        <button type="submit" class="btn">Simpan Perubahan</button>
        <a href="dokter.php" class="btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
</body>
</html>

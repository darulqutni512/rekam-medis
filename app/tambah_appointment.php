<?php
require_once __DIR__ . '/inc/db.php';
// ambil pasien dan dokter
$pasien_res = mysqli_query($koneksi, "SELECT id_pasien, nama_pasien FROM pasien ORDER BY nama_pasien");
$dokter_res = mysqli_query($koneksi, "SELECT id_dokter, nama_dokter FROM dokter ORDER BY nama_dokter");
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $id_pasien = (int)($_POST['id_pasien'] ?? 0);
  $id_dokter = isset($_POST['id_dokter']) && $_POST['id_dokter']!=='' ? (int)$_POST['id_dokter'] : null;
  $tanggal = trim($_POST['tanggal'] ?? '');
  $catatan = trim($_POST['catatan'] ?? '');
  if (!$id_pasien || $tanggal==='') $errors[]='Pasien dan tanggal wajib diisi.';
  if (empty($errors)){
    $stmt = mysqli_prepare($koneksi, "INSERT INTO appointment (id_pasien, id_dokter, tanggal, catatan) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iiss', $id_pasien, $id_dokter, $tanggal, $catatan);
    if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); header('Location: appointment.php'); exit; }
    $errors[]='Gagal menyimpan: '.mysqli_error($koneksi);
  }
}
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Tambah Janji</title>
<link rel="stylesheet" href="../style.css"></head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <h2>Tambah Janji</h2>
    <?php if(!empty($errors)): ?><div class="card" style="background:#ffecec;color:#a94442;padding:12px;margin-bottom:12px;"><?=htmlspecialchars(implode('\n',$errors))?></div><?php endif; ?>
    <form method="POST" class="form-box">
      <div class="form-row">
        <div class="form-group">
          <label>Pasien</label>
          <select name="id_pasien" required>
            <option value="">-- Pilih Pasien --</option>
            <?php mysqli_data_seek($pasien_res,0); while($p=mysqli_fetch_assoc($pasien_res)){ echo "<option value='{$p['id_pasien']}'>".htmlspecialchars($p['nama_pasien'])."</option>"; } ?>
          </select>
        </div>
        <div class="form-group">
          <label>Dokter (opsional)</label>
          <select name="id_dokter">
            <option value="">-- Pilih Dokter --</option>
            <?php mysqli_data_seek($dokter_res,0); while($d=mysqli_fetch_assoc($dokter_res)){ echo "<option value='{$d['id_dokter']}'>".htmlspecialchars($d['nama_dokter'])."</option>"; } ?>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Tanggal & Waktu</label>
          <input type="datetime-local" name="tanggal" required>
        </div>
        <div class="form-group">
          <label>Catatan</label>
          <textarea name="catatan"></textarea>
        </div>
      </div>
      <div class="actions mt-12">
        <button type="submit" class="btn">Simpan</button>
        <a href="appointment.php" class="btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
</body></html>

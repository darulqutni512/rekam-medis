<?php
require_once __DIR__ . '/inc/db.php';
$errors=[];
// fetch pasien/dokter/obat lists
$pasien = mysqli_query($koneksi, "SELECT id_pasien, nama_pasien FROM pasien ORDER BY nama_pasien");
$dokter = mysqli_query($koneksi, "SELECT id_dokter, nama_dokter FROM dokter ORDER BY nama_dokter");
$obats = mysqli_query($koneksi, "SELECT id_obat, nama_obat FROM obat ORDER BY nama_obat");

if ($_SERVER['REQUEST_METHOD']==='POST'){
  $id_pasien = (int)($_POST['id_pasien'] ?? 0);
  $id_dokter = isset($_POST['id_dokter']) && $_POST['id_dokter']!=='' ? (int)$_POST['id_dokter'] : null;
  $tanggal = trim($_POST['tanggal'] ?? date('Y-m-d H:i:s'));
  $keluhan = trim($_POST['keluhan'] ?? '');
  $diagnosa = trim($_POST['diagnosa'] ?? '');
  $tindakan = trim($_POST['tindakan'] ?? '');
  $keterangan = trim($_POST['keterangan'] ?? '');
  $obat_ids = $_POST['obat'] ?? [];
  $obat_notes = $_POST['obat_note'] ?? [];

  if (!$id_pasien) $errors[]='Pilih pasien.';
  if (empty($errors)){
    // ensure schema: add missing columns/tables if necessary
    $needs = [];
    $check = mysqli_query($koneksi, "SHOW COLUMNS FROM rekam_medis LIKE 'keluhan'"); if (!$check || mysqli_num_rows($check)===0) $needs[] = "ALTER TABLE rekam_medis ADD COLUMN keluhan TEXT";
    $check = mysqli_query($koneksi, "SHOW COLUMNS FROM rekam_medis LIKE 'diagnosa'"); if (!$check || mysqli_num_rows($check)===0) $needs[] = "ALTER TABLE rekam_medis ADD COLUMN diagnosa TEXT";
    $check = mysqli_query($koneksi, "SHOW COLUMNS FROM rekam_medis LIKE 'tindakan'"); if (!$check || mysqli_num_rows($check)===0) $needs[] = "ALTER TABLE rekam_medis ADD COLUMN tindakan TEXT";
    $check = mysqli_query($koneksi, "SHOW COLUMNS FROM rekam_medis LIKE 'keterangan'"); if (!$check || mysqli_num_rows($check)===0) $needs[] = "ALTER TABLE rekam_medis ADD COLUMN keterangan TEXT";
    $check = mysqli_query($koneksi, "SHOW TABLES LIKE 'rekam_obat'"); if (!$check || mysqli_num_rows($check)===0) $needs[] = "CREATE TABLE IF NOT EXISTS rekam_obat (id INT UNSIGNED NOT NULL AUTO_INCREMENT, id_rekam INT UNSIGNED NOT NULL, id_obat INT UNSIGNED NOT NULL, catatan VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    foreach($needs as $q){ if (!mysqli_query($koneksi,$q)) { $errors[] = 'Gagal memperbarui schema: '.mysqli_error($koneksi); } }
    if (!empty($errors)) { /* abort */ }

    // normalize datetime-local input (YYYY-MM-DDTHH:MM) to MySQL DATETIME
    if (strpos($tanggal,'T')!==false) {
      $tanggal = str_replace('T',' ',$tanggal);
      if (strlen($tanggal) === 16) $tanggal .= ':00';
    }

    // use NULLIF to allow id_dokter to be nullable when 0 passed
    $id_dokter_param = $id_dokter ?? 0;

    // start transaction
    mysqli_begin_transaction($koneksi);
    $stmt = mysqli_prepare($koneksi, "INSERT INTO rekam_medis (id_pasien, id_dokter, tanggal, keluhan, diagnosa, tindakan, keterangan) VALUES (?, NULLIF(?,0), ?, ?, ?, ?, ?)");
    if (!$stmt) {
      $errors[] = 'Gagal menyiapkan query rekam: '.mysqli_error($koneksi);
      mysqli_rollback($koneksi);
    } else {
      mysqli_stmt_bind_param($stmt,'iisssss',$id_pasien, $id_dokter_param, $tanggal, $keluhan, $diagnosa, $tindakan, $keterangan);
      if (mysqli_stmt_execute($stmt)){
        $id_rekam = mysqli_insert_id($koneksi);
        // insert obat relations
        if (is_array($obat_ids) && count($obat_ids)>0){
          $pstmt = mysqli_prepare($koneksi, "INSERT INTO rekam_obat (id_rekam, id_obat, catatan) VALUES (?, ?, ?)");
          if (!$pstmt) {
            $errors[] = 'Gagal menyiapkan query obat: '.mysqli_error($koneksi);
            mysqli_rollback($koneksi);
          } else {
            foreach ($obat_ids as $k => $oid){
              $note = trim($obat_notes[$k] ?? '');
              $oid_i = (int)$oid;
              mysqli_stmt_bind_param($pstmt,'iis',$id_rekam,$oid_i,$note);
              mysqli_stmt_execute($pstmt);
            }
            mysqli_stmt_close($pstmt);
            mysqli_commit($koneksi);
            mysqli_stmt_close($stmt);
            header('Location: rekam_medis.php'); exit;
          }
        } else {
          mysqli_commit($koneksi);
          mysqli_stmt_close($stmt);
          header('Location: rekam_medis.php'); exit;
        }
      } else {
        $errors[] = 'Gagal menyimpan rekam: '.mysqli_error($koneksi);
        mysqli_rollback($koneksi);
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Tambah Rekam Medis</title>
<link rel="stylesheet" href="../style.css"></head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <h2>Tambah Rekam Medis</h2>
    <?php if(!empty($errors)): ?><div class="card" style="background:#ffecec;color:#a94442;padding:12px;margin-bottom:12px;"><?=htmlspecialchars(implode('\n',$errors))?></div><?php endif; ?>
    <form method="POST" class="form-box">
      <label>Pasien</label>
      <select name="id_pasien" required>
        <option value="">-- Pilih Pasien --</option>
        <?php mysqli_data_seek($pasien,0); while($p=mysqli_fetch_assoc($pasien)){ echo "<option value='{$p['id_pasien']}'>".htmlspecialchars($p['nama_pasien'])."</option>"; } ?>
      </select>
      <label>Dokter (penanggung jawab)</label>
      <select name="id_dokter">
        <option value="">-- Pilih Dokter --</option>
        <?php mysqli_data_seek($dokter,0); while($d=mysqli_fetch_assoc($dokter)){ echo "<option value='{$d['id_dokter']}'>".htmlspecialchars($d['nama_dokter'])."</option>"; } ?>
      </select>
      <label>Tanggal & Waktu</label>
      <input type="datetime-local" name="tanggal" value="<?= date('Y-m-d\TH:i') ?>">
      <label>Keluhan</label>
      <textarea name="keluhan"></textarea>
      <label>Diagnosa</label>
      <textarea name="diagnosa"></textarea>
      <label>Tindakan</label>
      <textarea name="tindakan"></textarea>
      <label>Resep Obat (pilih dan isi catatan)</label>
      <div>
        <?php mysqli_data_seek($obats,0); $i=0; while($o=mysqli_fetch_assoc($obats)){ ?>
          <div class="flex vcenter" style="gap:8px;margin-bottom:6px;">
            <input type="checkbox" name="obat[]" value="<?= (int)$o['id_obat'] ?>"> <?= htmlspecialchars($o['nama_obat']) ?>
            <input type="text" name="obat_note[]" placeholder="catatan dosis (opsional)" style="flex:1;padding:6px;border-radius:6px;border:1px solid #ccc;">
          </div>
        <?php $i++; } ?>
      </div>
      <label>Keterangan</label>
      <textarea name="keterangan"></textarea>
      <div class="actions mt-12"><button type="submit" class="btn">Simpan Rekam</button> <a class="btn-secondary" href="rekam_medis.php">Kembali</a></div>
    </form>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
</body></html>

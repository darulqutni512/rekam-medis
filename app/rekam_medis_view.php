<?php
require_once __DIR__ . '/inc/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0; if ($id<=0) { header('Location: rekam_medis.php'); exit; }
// fetch rekam
$stmt = mysqli_prepare($koneksi, "SELECT r.*, p.nama_pasien, d.nama_dokter FROM rekam_medis r LEFT JOIN pasien p ON r.id_pasien=p.id_pasien LEFT JOIN dokter d ON r.id_dokter=d.id_dokter WHERE r.id_rekam=?");
mysqli_stmt_bind_param($stmt,'i',$id); mysqli_stmt_execute($stmt); $res = mysqli_stmt_get_result($stmt); $rek = mysqli_fetch_assoc($res); mysqli_stmt_close($stmt);
if (!$rek) { header('Location: rekam_medis.php'); exit; }
// fetch obat
$ores = mysqli_prepare($koneksi, "SELECT o.nama_obat, ro.catatan FROM rekam_obat ro JOIN obat o ON ro.id_obat=o.id_obat WHERE ro.id_rekam=?"); mysqli_stmt_bind_param($ores,'i',$id); mysqli_stmt_execute($ores); $obres = mysqli_stmt_get_result($ores); mysqli_stmt_close($ores);
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Detail Rekam</title>
<link rel="stylesheet" href="../style.css"></head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <h2>Detail Rekam Medis #<?= (int)$rek['id_rekam'] ?></h2>
    <p><strong>Pasien:</strong> <?= htmlspecialchars($rek['nama_pasien']) ?></p>
    <p><strong>Dokter:</strong> <?= htmlspecialchars($rek['nama_dokter']) ?></p>
    <p><strong>Tanggal:</strong> <?= htmlspecialchars($rek['tanggal']) ?></p>
    <p><strong>Keluhan:</strong><br><?= nl2br(htmlspecialchars($rek['keluhan'])) ?></p>
    <p><strong>Diagnosa:</strong><br><?= nl2br(htmlspecialchars($rek['diagnosa'])) ?></p>
    <p><strong>Tindakan:</strong><br><?= nl2br(htmlspecialchars($rek['tindakan'])) ?></p>
    <p><strong>Obat:</strong></p>
    <ul>
      <?php while($o = mysqli_fetch_assoc($obres)){ echo '<li>' . htmlspecialchars($o['nama_obat']) . ' - ' . htmlspecialchars($o['catatan']) . '</li>'; } ?>
    </ul>
    <p><strong>Keterangan:</strong><br><?= nl2br(htmlspecialchars($rek['keterangan'])) ?></p>
    <a href="rekam_medis.php" class="btn-secondary">Kembali</a>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
</body></html>

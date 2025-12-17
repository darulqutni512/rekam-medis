<?php
require_once __DIR__ . '/inc/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Beranda - Rekam Medis (Rebuild)</title>
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="home.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <section class="card">
    <div class="home-hero">
      <div class="left">
        <h2>Selamat Datang di Sistem Rekam Medis</h2>
        <p>Antarmuka bersih dan terstruktur untuk memudahkan pengelolaan data pasien, dokter, obat, janji, dan rekam medis.</p>
        <div style="margin-top:12px;color:#444">Waktu saat ini: <strong id="timeNow"><?= htmlspecialchars(date('Y-m-d H:i:s')) ?></strong></div>
        <?php
        $cDokter = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM dokter"))['c'] ?? 0;
        $cPasien = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM pasien"))['c'] ?? 0;
        $cObat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM obat"))['c'] ?? 0;
        $cJanji = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS c FROM appointment WHERE status='scheduled'"))['c'] ?? 0;
        ?>
        <div class="kpi-row">
          <div class="stat-card">
            <div class="stat-number"><?= (int)$cPasien ?></div>
            <div>Pasien</div>
          </div>
          <div class="stat-card">
            <div class="stat-number"><?= (int)$cDokter ?></div>
            <div>Dokter</div>
          </div>
          <div class="stat-card">
            <div class="stat-number"><?= (int)$cObat ?></div>
            <div>Jenis Obat</div>
          </div>
          <div class="stat-card">
            <div class="stat-number"><?= (int)$cJanji ?></div>
            <div>Janji</div>
          </div>

          <div style="flex:1;min-width:160px;background:linear-gradient(90deg,#f0fbff,#ffffff);border-radius:12px;padding:12px;box-shadow:0 6px 18px rgba(0,0,0,0.04);">
            <h4 style="margin:0 0 6px 0;">Ringkasan Proporsi</h4>
            <svg width="120" height="120" viewBox="0 0 42 42" class="donut">
              <circle cx="21" cy="21" r="15" fill="transparent" stroke="#eef6fb" stroke-width="6"></circle>
              <?php
                $total = max(1, $cPasien + $cDokter + $cObat + $cJanji);
                $a1 = round(($cPasien/$total)*100);
                $a2 = round(($cDokter/$total)*100);
                $a3 = round(($cObat/$total)*100);
                $a4 = 100 - ($a1+$a2+$a3);
              ?>
              <circle transform="rotate(-90 21 21)" cx="21" cy="21" r="15" fill="transparent" stroke="#0077b6" stroke-width="6" stroke-dasharray="<?= $a1 ?> 100" stroke-dashoffset="0"></circle>
              <circle transform="rotate(-90 21 21)" cx="21" cy="21" r="15" fill="transparent" stroke="#00b4d8" stroke-width="6" stroke-dasharray="<?= $a2 ?> 100" stroke-dashoffset="-<?= $a1 ?>"></circle>
              <circle transform="rotate(-90 21 21)" cx="21" cy="21" r="15" fill="transparent" stroke="#90e0ef" stroke-width="6" stroke-dasharray="<?= $a3 ?> 100" stroke-dashoffset="-<?= ($a1+$a2) ?>"></circle>
              <circle transform="rotate(-90 21 21)" cx="21" cy="21" r="15" fill="transparent" stroke="#caf0f8" stroke-width="6" stroke-dasharray="<?= $a4 ?> 100" stroke-dashoffset="-<?= ($a1+$a2+$a3) ?>"></circle>
            </svg>
            <div style="font-size:12px;margin-top:6px">Pasien / Dokter / Obat / Janji</div>
          </div>
        </div>

        <div class="quick-actions" style="margin-top:14px">
          <a class="action" href="pasien.php"><img class="icon" src="../assets/user.svg" alt=""><div class="label">Pasien</div><div class="muted">Kelola data</div></a>
          <a class="action" href="dokter.php"><img class="icon" src="../assets/doctor.svg" alt=""><div class="label">Dokter</div><div class="muted">Atur dokter</div></a>
          <a class="action" href="obat.php"><img class="icon" src="../assets/pill.svg" alt=""><div class="label">Obat</div><div class="muted">Stok & resep</div></a>
          <a class="action" href="appointment.php"><img class="icon" src="../assets/calendar.svg" alt=""><div class="label">Janji</div><div class="muted">Buat janji</div></a>
          <a class="action" href="rekam_medis.php"><img class="icon" src="../assets/notes.svg" alt=""><div class="label">Rekam</div><div class="muted">Catatan medis</div></a>
        </div>
      </div>
      <div class="right">
        <img src="../assets/logo.png" alt="Ilustrasi" />
      </div>
    </div>

    <div class="recent-list" style="margin-top:18px">
      <div class="recent-card">
        <h3 style="margin-top:0">Pasien Terbaru</h3>
        <ul>
          <?php $rp = mysqli_query($koneksi, "SELECT id_pasien, nama_pasien, nik FROM pasien ORDER BY id_pasien DESC LIMIT 5"); while($row=mysqli_fetch_assoc($rp)){ echo '<li><a href="pasien.php">'.htmlspecialchars($row['nama_pasien']).' ('.htmlspecialchars($row['nik']).')</a></li>'; } ?>
        </ul>
      </div>
      <div class="recent-card">
        <h3 style="margin-top:0">Janji Terdekat</h3>
        <ul>
          <?php $rj = mysqli_query($koneksi, "SELECT a.id_appointment, p.nama_pasien, a.tanggal FROM appointment a JOIN pasien p ON a.id_pasien=p.id_pasien WHERE a.status='scheduled' ORDER BY a.tanggal ASC LIMIT 5"); while($row=mysqli_fetch_assoc($rj)){ echo '<li>'.htmlspecialchars($row['nama_pasien']).' - '.htmlspecialchars($row['tanggal']).'</li>'; } ?>
        </ul>
      </div>
    </div>
  </section>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
<script>
// real time clock update
setInterval(()=>{
  const el=document.getElementById('timeNow');
  if(el){
    const d=new Date();
    el.textContent = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0') + ' ' + String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0') + ':' + String(d.getSeconds()).padStart(2,'0');
  }
},1000);
</script>
<script src="home.js"></script>
</body>
</html>

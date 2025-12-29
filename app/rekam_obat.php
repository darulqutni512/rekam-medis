<?php
require_once __DIR__ . '/inc/db.php';
$recent = [];
$res = mysqli_query($koneksi, "SELECT ro.id, p.nama_pasien, o.nama_obat, rm.tanggal, ro.catatan FROM rekam_obat ro JOIN rekam_medis rm ON ro.id_rekam=rm.id_rekam LEFT JOIN pasien p ON rm.id_pasien=p.id_pasien LEFT JOIN obat o ON ro.id_obat=o.id_obat ORDER BY rm.tanggal DESC LIMIT 20");
if ($res) while($r=mysqli_fetch_assoc($res)) $recent[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Rekam Obat - Rekam Medis</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <div class="flex space-between vcenter">
      <h2>Rekam Obat</h2>
      <div class="flex">
        <input id="searchRekam" class="input-search wide" placeholder="Cari pasien atau obat...">
        <a href="tambah_rekam_obat.php" class="btn">+ Rekam Pemberian Obat</a>
      </div>
    </div>
    <table class="data-table">
      <thead><tr><th>ID</th><th>Pasien</th><th>Obat</th><th>Jumlah</th><th>Tanggal</th></tr></thead>
      <tbody>
        <?php if (empty($recent)): ?>
          <tr><td colspan="5">Belum ada rekam obat.</td></tr>
        <?php else: foreach($recent as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['nama_pasien']) ?></td>
            <td><?= htmlspecialchars($r['nama_obat']) ?></td>
            <td><?php $j = 0; if (!empty($r['catatan']) && preg_match('/Jumlah:\\s*(\\d+)/i', $r['catatan'], $m)) $j = (int)$m[1]; echo $j ? (int)$j : '-'; ?></td>
            <td><?= htmlspecialchars($r['tanggal']) ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
<script>
// search filter for rekam obat
document.getElementById('searchRekam')?.addEventListener('input', e=>{
  const q = e.target.value.toLowerCase();
  document.querySelectorAll('table.data-table tbody tr').forEach(tr=>{
    const pasien = tr.children[1]?.textContent.toLowerCase() || '';
    const obat = tr.children[2]?.textContent.toLowerCase() || '';
    tr.style.display = (pasien.includes(q) || obat.includes(q)) ? '' : 'none';
  });
});
</script>
</body>
</html>
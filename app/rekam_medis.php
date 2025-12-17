<?php
require_once __DIR__ . '/inc/db.php';
// Very small listing that links pasien to rekam_medis if exists in other pages
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Rekam Medis - Rekam Medis (Rebuild)</title>
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="rebuild.css">
</head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <div class="flex space-between vcenter">
      <h2>Rekam Medis</h2>
      <div class="flex">
        <input id="searchRekam" class="input-search wide" placeholder="Cari pasien, dokter, atau diagnosa...">
        <a href="rekam_medis_create.php" class="btn">+ Tambah Rekam</a>
      </div>
    </div>

    <table class="data-table">
      <thead>
        <tr><th>ID</th><th>Pasien</th><th>Dokter</th><th>Tanggal</th><th>Diagnosa singkat</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php
      $res = mysqli_query($koneksi, "SELECT r.id_rekam, p.nama_pasien, d.nama_dokter, r.tanggal, LEFT(r.diagnosa,80) AS diag FROM rekam_medis r LEFT JOIN pasien p ON r.id_pasien=p.id_pasien LEFT JOIN dokter d ON r.id_dokter=d.id_dokter ORDER BY r.tanggal DESC");
      if ($res && mysqli_num_rows($res) > 0) {
        while ($r = mysqli_fetch_assoc($res)) {
          echo "<tr>\n<td>".(int)$r['id_rekam']."</td>\n<td>".htmlspecialchars($r['nama_pasien'])."</td>\n<td>".htmlspecialchars($r['nama_dokter'])."</td>\n<td>".htmlspecialchars($r['tanggal'])."</td>\n<td>".htmlspecialchars($r['diag'])."</td>\n<td class='actions'><a class='btn-small' href='rekam_medis_view.php?id=".(int)$r['id_rekam']."'>Lihat</a> <a class='btn-danger' href='hapus_rekam.php?id=".(int)$r['id_rekam']."' onclick=\"return confirm('Yakin ingin menghapus rekam ini?')\">Hapus</a></td>\n</tr>";
        }
      } else {
        echo "<tr><td colspan='6'>Belum ada rekam medis.</td></tr>";
      }
      ?>
      </tbody>
    </table>
  </div>
</main>
<script>
document.getElementById('searchRekam')?.addEventListener('input', function(e){
  const q = e.target.value.toLowerCase();
  document.querySelectorAll('table.data-table tbody tr').forEach(function(tr){
    const pasien = (tr.children[1]?.textContent || '').toLowerCase();
    const dokter = (tr.children[2]?.textContent || '').toLowerCase();
    const diag = (tr.children[4]?.textContent || '').toLowerCase();
    tr.style.display = (pasien.includes(q) || dokter.includes(q) || diag.includes(q)) ? '' : 'none';
  });
});
</script>
<?php include __DIR__ . '/inc/footer.php'; ?>
</body>
</html>

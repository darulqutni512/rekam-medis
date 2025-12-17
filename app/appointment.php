<?php
require_once __DIR__ . '/inc/db.php';
$apps = [];
$res = mysqli_query($koneksi, "SELECT a.id_appointment, a.tanggal, a.catatan, p.nama_pasien, d.nama_dokter FROM appointment a LEFT JOIN pasien p ON a.id_pasien=p.id_pasien LEFT JOIN dokter d ON a.id_dokter=d.id_dokter ORDER BY a.tanggal DESC");
if ($res) while ($r = mysqli_fetch_assoc($res)) $apps[] = $r;
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Janji - Rekam Medis (Rebuild)</title>
<link rel="stylesheet" href="../style.css"></head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <div class="flex space-between vcenter">
      <h2>Daftar Janji</h2>
      <div class="flex">
        <input id="searchAppointment" class="input-search wide" placeholder="Cari pasien, dokter, atau tanggal...">
        <a href="tambah_appointment.php" class="btn">+ Tambah Janji</a>
      </div>
    </div>
    <table class="data-table">
      <thead><tr><th>ID</th><th>Pasien</th><th>Dokter</th><th>Tanggal</th><th>Catatan</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php if (empty($apps)): ?><tr><td colspan="6">Belum ada janji.</td></tr><?php else: foreach($apps as $a): ?>
        <tr>
          <td><?= (int)$a['id_appointment'] ?></td>
          <td><?= htmlspecialchars($a['nama_pasien']) ?></td>
          <td><?= htmlspecialchars($a['nama_dokter']) ?></td>
          <td><?= htmlspecialchars($a['tanggal']) ?></td>
          <td><?= htmlspecialchars($a['catatan']) ?></td>
          <td class="actions"><a class="btn-small" href="edit_appointment.php?id=<?= (int)$a['id_appointment'] ?>">Edit</a> <a class="btn-danger" href="hapus_appointment.php?id=<?= (int)$a['id_appointment'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<script>
document.getElementById('searchAppointment')?.addEventListener('input', function(e){
  const q = e.target.value.toLowerCase();
  document.querySelectorAll('table.data-table tbody tr').forEach(function(tr){
    const pasien = (tr.children[1]?.textContent || '').toLowerCase();
    const dokter = (tr.children[2]?.textContent || '').toLowerCase();
    const tanggal = (tr.children[3]?.textContent || '').toLowerCase();
    const catatan = (tr.children[4]?.textContent || '').toLowerCase();
    tr.style.display = (pasien.includes(q) || dokter.includes(q) || tanggal.includes(q) || catatan.includes(q)) ? '' : 'none';
  });
});
</script>
<?php include __DIR__ . '/inc/footer.php'; ?>
</body></html>

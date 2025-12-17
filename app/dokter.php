<?php
require_once __DIR__ . '/inc/db.php';
$stmt = mysqli_prepare($koneksi, "SELECT id_dokter, nama_dokter, spesialisasi, no_sip FROM dokter ORDER BY id_dokter DESC");
$dokter = [];
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) while ($r = mysqli_fetch_assoc($res)) $dokter[] = $r;
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dokter - Rekam Medis (Rebuild)</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <div class="flex space-between vcenter">
      <h2 class="no-margin">Daftar Dokter</h2>
      <div class="flex">
        <input id="searchDokter" class="input-search wide" placeholder="Cari nama atau spesialis">
        <a href="tambah_dokter.php" class="btn">+ Tambah Dokter</a>
      </div>
    </div>

    <table class="data-table" id="dokterTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Spesialis</th>
          <th>No. SIP</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($dokter) === 0): ?>
          <tr><td colspan="5">Belum ada data dokter.</td></tr>
        <?php else: foreach ($dokter as $d): ?>
          <tr>
            <td><?= (int)$d['id_dokter'] ?></td>
            <td><?= htmlspecialchars($d['nama_dokter']) ?></td>
            <td><?= htmlspecialchars($d['spesialisasi']) ?></td>
            <td><?= htmlspecialchars($d['no_sip']) ?></td>
            <td class="actions">
              <a href="edit_dokter.php?id=<?= (int)$d['id_dokter'] ?>" class="btn-small">Edit</a>
              <a href="hapus_dokter.php?id=<?= (int)$d['id_dokter'] ?>" class="btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
<script>
const searchDok = document.getElementById('searchDokter');
searchDok.addEventListener('input', e => {
  const q = e.target.value.toLowerCase();
  document.querySelectorAll('#dokterTable tbody tr').forEach(tr => {
    const name = tr.children[1]?.textContent.toLowerCase() || '';
    const spec = tr.children[2]?.textContent.toLowerCase() || '';
    tr.style.display = (name.includes(q) || spec.includes(q)) ? '' : 'none';
  });
});
</script>
</body>
</html>

<?php
require_once __DIR__ . '/inc/db.php';
// fetch pasien list safely
$stmt = mysqli_prepare($koneksi, "SELECT id_pasien, nama_pasien, nik, tanggal_lahir, jenis_kelamin, no_telepon, alamat FROM pasien ORDER BY id_pasien DESC");
$pasien = [];
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) $pasien[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pasien - Rekam Medis (Rebuild)</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <div class="flex space-between vcenter">
      <h2 class="no-margin">Daftar Pasien</h2>
      <div class="flex">
        <input id="searchInput" class="input-search wide" placeholder="Cari nama atau NIK...">
        <a href="tambah_pasien.php" class="btn">+ Tambah Pasien</a>
      </div>
    </div>

    <table class="data-table" id="pasienTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>NIK</th>
          <th>Tgl Lahir</th>
          <th>JK</th>
          <th>No. Telp</th>
          <th>Alamat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($pasien) === 0): ?>
          <tr><td colspan="8">Belum ada data pasien.</td></tr>
        <?php else: foreach ($pasien as $p): ?>
          <tr>
            <td><?= (int)$p['id_pasien'] ?></td>
            <td><?= htmlspecialchars($p['nama_pasien']) ?></td>
            <td><?= htmlspecialchars($p['nik']) ?></td>
            <td><?= htmlspecialchars($p['tanggal_lahir']) ?></td>
            <td><?= htmlspecialchars($p['jenis_kelamin']) ?></td>
            <td><?= htmlspecialchars($p['no_telepon']) ?></td>
            <td style="max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($p['alamat']) ?></td>
            <td class="aksi-btn">
              <a href="edit_pasien.php?id=<?= (int)$p['id_pasien'] ?>" class="btn-small">Edit</a>
              <a href="hapus_pasien.php?id=<?= (int)$p['id_pasien'] ?>" class="btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
<script>
document.getElementById('searchInput').addEventListener('input', function(e){
  const q = e.target.value.toLowerCase();
  document.querySelectorAll('#pasienTable tbody tr').forEach(tr => {
    const name = tr.children[1]?.textContent.toLowerCase() || '';
    const nik = tr.children[2]?.textContent.toLowerCase() || '';
    tr.style.display = (name.includes(q) || nik.includes(q)) ? '' : 'none';
  });
});
</script>
</body>
</html>

<?php
require_once __DIR__ . '/inc/db.php';
$obats = [];
$res = mysqli_query($koneksi, "SELECT id_obat, nama_obat, stok, satuan, keterangan FROM obat ORDER BY id_obat DESC");
if ($res) while ($r = mysqli_fetch_assoc($res)) $obats[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Obat - Rekam Medis (Rebuild)</title>
<link rel="stylesheet" href="../style.css"></head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <div class="flex space-between vcenter">
      <h2>Daftar Obat</h2>
      <div class="flex">
        <input id="searchObat" class="input-search wide" placeholder="Cari nama obat...">
        <a href="tambah_obat.php" class="btn">+ Tambah Obat</a>
      </div>
    </div>
    <table class="data-table" id="obatTable">
      <thead><tr><th>ID</th><th>Nama</th><th>Stok</th><th>Satuan</th><th>Keterangan</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php if (empty($obats)): ?><tr><td colspan="6">Belum ada data obat.</td></tr><?php else: foreach ($obats as $o): ?>
          <tr>
            <td><?= (int)$o['id_obat'] ?></td>
            <td><?= htmlspecialchars($o['nama_obat']) ?></td>
            <td><?= (int)$o['stok'] ?></td>
            <td><?= htmlspecialchars($o['satuan']) ?></td>
            <td><?= htmlspecialchars($o['keterangan']) ?></td>
            <td class="actions">
              <a href="edit_obat.php?id=<?= (int)$o['id_obat'] ?>" class="btn-small">Edit</a>
              <a href="hapus_obat.php?id=<?= (int)$o['id_obat'] ?>" class="btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
<script>
document.getElementById('searchObat')?.addEventListener('input', e=>{
  const q = e.target.value.toLowerCase();
  document.querySelectorAll('#obatTable tbody tr').forEach(tr=>{
    tr.style.display = tr.children[1].textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
</script>
</body>
</html>

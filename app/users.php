<?php
require_once __DIR__ . '/inc/db.php';
// Halaman users telah dihapus; redirect ke beranda
header("Location: ./");
exit;
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Users - Rekam Medis (Rebuild)</title>
<link rel="stylesheet" href="../style.css"></head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <div class="flex space-between vcenter">
      <h2>Manajemen Users</h2>
      <div class="flex">
        <input id="searchUsers" class="input-search wide" placeholder="Cari username atau nama...">
        <a href="tambah_user.php" class="btn">+ Tambah User</a>
      </div>
    </div>
    <table class="data-table" id="usersTable">
      <thead><tr><th>ID</th><th>Username</th><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php if (empty($users)): ?><tr><td colspan="6">Belum ada user.</td></tr><?php else: foreach($users as $u): ?>
        <tr>
          <td><?= (int)$u['id_user'] ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= htmlspecialchars($u['nama']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['role']) ?></td>
          <td class="actions"><a class="btn-small" href="edit_user.php?id=<?= (int)$u['id_user'] ?>">Edit</a> <a class="btn-danger" href="hapus_user.php?id=<?= (int)$u['id_user'] ?>" onclick="return confirm('Hapus user ini?')">Hapus</a></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
<script>
document.getElementById('searchUsers')?.addEventListener('input',e=>{
  const q=e.target.value.toLowerCase();document.querySelectorAll('#usersTable tbody tr').forEach(tr=>{tr.style.display=(tr.children[1].textContent.toLowerCase().includes(q)||tr.children[2].textContent.toLowerCase().includes(q))? '':'none';});
});
</script>
</body></html>

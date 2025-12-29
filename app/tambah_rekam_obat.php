<?php
require_once __DIR__ . '/inc/db.php';
// handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_pasien = (int)($_POST['id_pasien'] ?? 0);
  $id_obat = (int)($_POST['id_obat'] ?? 0);
  $jumlah = (int)($_POST['jumlah'] ?? 0);
  $tanggal = $_POST['tanggal'] ?? date('Y-m-d H:i:s');
  // normalize datetime-local to MySQL
  if (strpos($tanggal, 'T') !== false) $tanggal = str_replace('T', ' ', $tanggal);
  if ($id_pasien && $id_obat && $jumlah) {
    // create a minimal rekam_medis entry and link the obat to it
    $tindakan = 'Pemberian obat';
    $id_rekam = 0;
    $stmt = mysqli_prepare($koneksi, "INSERT INTO rekam_medis (id_pasien, tanggal, tindakan) VALUES (?, ?, ?)");
    if ($stmt) {
      mysqli_stmt_bind_param($stmt, 'iss', $id_pasien, $tanggal, $tindakan);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
      $id_rekam = mysqli_insert_id($koneksi);
    } else {
      mysqli_query($koneksi, "INSERT INTO rekam_medis (id_pasien, tanggal, tindakan) VALUES (".intval($id_pasien).",'".mysqli_real_escape_string($koneksi,$tanggal)."','".mysqli_real_escape_string($koneksi,$tindakan)."')");
      $id_rekam = mysqli_insert_id($koneksi);
    }

    if ($id_rekam) {
      $catatan = 'Jumlah: '.intval($jumlah);
      $pstmt = mysqli_prepare($koneksi, "INSERT INTO rekam_obat (id_rekam, id_obat, catatan) VALUES (?, ?, ?)");
      if ($pstmt) {
        mysqli_stmt_bind_param($pstmt, 'iis', $id_rekam, $id_obat, $catatan);
        mysqli_stmt_execute($pstmt);
        mysqli_stmt_close($pstmt);
      } else {
        mysqli_query($koneksi, "INSERT INTO rekam_obat (id_rekam, id_obat, catatan) VALUES (".intval($id_rekam).",".intval($id_obat).",'".mysqli_real_escape_string($koneksi,$catatan)."')");
      }
      // decrement stok (best-effort)
      mysqli_query($koneksi, "UPDATE obat SET stok = GREATEST(0, stok - ".intval($jumlah).") WHERE id_obat = ".intval($id_obat));

      header('Location: rekam_obat.php');
      exit;
    } else {
      $error = 'Gagal membuat rekam medis.';
    }
  } else {
    $error = 'Lengkapi semua field.';
  }
}
// fetch patients and obat
$pasien = [];
$res = mysqli_query($koneksi, "SELECT id_pasien, nama_pasien FROM pasien ORDER BY nama_pasien"); if ($res) while($r=mysqli_fetch_assoc($res)) $pasien[]=$r;
$obat = [];
$res2 = mysqli_query($koneksi, "SELECT id_obat, nama_obat FROM obat ORDER BY nama_obat"); if ($res2) while($r=mysqli_fetch_assoc($res2)) $obat[]=$r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Rekam Obat</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include __DIR__ . '/inc/header.php'; ?>
<main class="container">
  <div class="card">
    <h2>Rekam Pemberian Obat</h2>
    <?php if (!empty($error)): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
      <label>Pasien</label>
      <input list="listPasien" id="input_pasien" class="input-search" placeholder="Ketik nama atau pilih..." value="<?= htmlspecialchars($_POST['pasien_name'] ?? '') ?>" autocomplete="off" autofocus>
      <datalist id="listPasien">
        <?php foreach($pasien as $p): ?>
          <option data-id="<?= (int)$p['id_pasien'] ?>" value="<?= htmlspecialchars($p['nama_pasien']) ?>"></option>
        <?php endforeach; ?>
      </datalist>
      <input type="hidden" name="id_pasien" id="id_pasien" value="<?= (int)($_POST['id_pasien'] ?? 0) ?>">

      <label>Obat</label>
      <input list="listObat" id="input_obat" class="input-search" placeholder="Ketik nama obat..." value="<?= htmlspecialchars($_POST['obat_name'] ?? '') ?>" autocomplete="off">
      <datalist id="listObat">
        <?php foreach($obat as $o): ?>
          <option data-id="<?= (int)$o['id_obat'] ?>" value="<?= htmlspecialchars($o['nama_obat']) ?>"></option>
        <?php endforeach; ?>
      </datalist>
      <input type="hidden" name="id_obat" id="id_obat" value="<?= (int)($_POST['id_obat'] ?? 0) ?>">

      <label>Jumlah</label>
      <input type="number" name="jumlah" min="1" value="<?= (int)($_POST['jumlah'] ?? 1) ?>" required>

      <label>Tanggal</label>
      <input type="datetime-local" name="tanggal" value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d\\TH:i')) ?>">

      <div style="margin-top:12px">
        <button class="btn" type="submit">Simpan</button>
        <a class="btn" href="rekam_obat.php">Batal</a>
      </div>
    </form>
  </div>
</main>
<?php include __DIR__ . '/inc/footer.php'; ?>
<script>
// improved sinkronisasi datalist ke hidden id dan validasi form
(function(){
  // maps generated from server
  const pasienMap = <?php echo json_encode(array_column($pasien,'id_pasien','nama_pasien'), JSON_UNESCAPED_UNICODE); ?>;
  const obatMap = <?php echo json_encode(array_column($obat,'id_obat','nama_obat'), JSON_UNESCAPED_UNICODE); ?>;

  function sync(listId,inputId,hiddenId,map){
    const input=document.getElementById(inputId); const hidden=document.getElementById(hiddenId); const list=document.getElementById(listId);
    if(!input||!hidden) return;
    input.addEventListener('input', ()=>{
      const val = input.value.trim(); let foundId = 0;
      if(map && Object.prototype.hasOwnProperty.call(map, val)){
        foundId = map[val];
      } else if(list){
        Array.from(list.options).forEach(opt=>{ if(opt.value===val){ foundId = opt.getAttribute('data-id') || foundId; }});
      }
      hidden.value = parseInt(foundId) || 0;
    });

    // press Enter to move to next field
    input.addEventListener('keydown', e=>{
      if(e.key === 'Enter'){
        e.preventDefault();
        const focusable = Array.from(document.querySelectorAll('input, select, button, textarea')).filter(el=>!el.disabled && el.type !== 'hidden');
        const idx = focusable.indexOf(input);
        if(idx >= 0 && idx < focusable.length - 1) focusable[idx+1].focus();
      }
    });
  }

  sync('listPasien','input_pasien','id_pasien', pasienMap);
  sync('listObat','input_obat','id_obat', obatMap);

  document.querySelector('form')?.addEventListener('submit', function(e){
    const pid = parseInt(document.getElementById('id_pasien')?.value||0);
    const oid = parseInt(document.getElementById('id_obat')?.value||0);
    if(!pid || !oid){ e.preventDefault(); alert('Pilih pasien dan obat dari daftar yang tersedia.'); }
  });

  // autofocus pasien input
  document.getElementById('input_pasien')?.focus();
})();
</script>
</body>
</html>
<?php
require_once __DIR__ . '/inc/db.php';
function count_table($koneksi,$t){ $r=mysqli_query($koneksi,"SELECT COUNT(*) AS c FROM `$t`"); return $r?intval(mysqli_fetch_assoc($r)['c']):0; }
$tables = ['dokter','pasien','obat','appointment','rekam_medis','rekam_obat'];
$info = [];
foreach($tables as $t) $info[$t]=count_table($koneksi,$t);
header('Content-Type: application/json');
echo json_encode(['counts'=>$info,'sample'=>[ 
  'dokter'=> mysqli_fetch_all(mysqli_query($koneksi,'SELECT * FROM dokter ORDER BY id_dokter DESC LIMIT 5'),MYSQLI_ASSOC),
  'pasien'=> mysqli_fetch_all(mysqli_query($koneksi,'SELECT * FROM pasien ORDER BY id_pasien DESC LIMIT 5'),MYSQLI_ASSOC),
  'obat'=> mysqli_fetch_all(mysqli_query($koneksi,'SELECT * FROM obat ORDER BY id_obat DESC LIMIT 5'),MYSQLI_ASSOC),
  'rekam'=> mysqli_fetch_all(mysqli_query($koneksi,'SELECT * FROM rekam_medis ORDER BY tanggal DESC LIMIT 5'),MYSQLI_ASSOC),
  'rekam_obat'=> mysqli_fetch_all(mysqli_query($koneksi,'SELECT * FROM rekam_obat ORDER BY id DESC LIMIT 5'),MYSQLI_ASSOC)
]]);
?>

<?php
// Simple DB include wrapper used by rebuilt app pages
// Try common locations for koneksi.php (project root, app folder)
$candidates = [
    __DIR__ . '/../../koneksi.php', // project root
    __DIR__ . '/../koneksi.php',    // app/koneksi.php
    __DIR__ . '/koneksi.php'
];
$koneksiPath = null;
foreach ($candidates as $c) {
    if (file_exists($c)) { $koneksiPath = $c; break; }
}
if ($koneksiPath) {
    require_once $koneksiPath;
    if (!isset($koneksi) || !$koneksi) {
        throw new RuntimeException('Koneksi database gagal. Periksa konfigurasi koneksi.php');
    }
} else {
    throw new RuntimeException('File koneksi.php tidak ditemukan di project (searched: ' . implode(', ', $candidates) . ')');
}
?>

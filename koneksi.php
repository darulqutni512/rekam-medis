<?php
// Simple koneksi.php created by rebuild script. Sesuaikan kredensial jika perlu.
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'db_rekammedis';

mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = @mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    $err = mysqli_connect_error();
    if (strpos($err, 'Unknown database') !== false) {
        $koneksi = @mysqli_connect($host, $user, $pass);
        if ($koneksi && file_exists(__DIR__ . '/create_db.sql')) {
            $sql = file_get_contents(__DIR__ . '/create_db.sql');
            if ($sql !== false) {
                if (mysqli_multi_query($koneksi, $sql)) {
                    do { } while (mysqli_more_results($koneksi) && mysqli_next_result($koneksi));
                    mysqli_select_db($koneksi, $db);
                }
            }
        }
    }
}
if (!$koneksi) {
    // If still no connection, throw friendly error for dev environment
    echo "<h2>Gagal koneksi ke database</h2><pre>" . htmlspecialchars($err ?? 'unknown') . "</pre>";
    exit;
}
?>

<?php
// Konfigurasi database
$host = 'localhost';     // Host database
$username = 'root';      // Username database
$password = '';          // Password database
$database = 'kredit_bni'; // Nama database

// Membuat koneksi ke database menggunakan pendekatan OOP
try {
    $koneksi = new mysqli($host, $username, $password, $database);

    // Cek koneksi
    if ($koneksi->connect_error) {
        throw new Exception("Koneksi database gagal: " . $koneksi->connect_error);
    }

    // Set karakter encoding
    $koneksi->set_charset("utf8");

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Fungsi untuk mencegah SQL Injection
function anti_injection($data) {
    global $koneksi;
    $filter = $koneksi->real_escape_string(stripslashes(strip_tags(htmlspecialchars($data, ENT_QUOTES))));
    return $filter;
}

// Fungsi untuk format tanggal Indonesia
function tanggal_indo($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Fungsi untuk format angka ke rupiah
function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

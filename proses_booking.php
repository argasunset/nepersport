<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Akses ditolak!'];
    header("Location: landing_page.php");
    exit();
}

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    $_SESSION['alert'] = ["type" => "warning", "message" => "Anda harus login terlebih dahulu untuk booking!"];
    header("Location: landing_page.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$nama_user = htmlspecialchars($_POST['nama_user']);
$jadwal_booking = $_POST['jadwal_booking'];
$jam = $_POST['jam'];
$via_pembayaran = $_POST['via_pembayaran'];
$nomor_pembayaran = $_POST['nomor_pembayaran'] ?? '';

if (empty($nama_user) || empty($jadwal_booking) || empty($jam) || empty($via_pembayaran)) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Harap isi semua kolom wajib!'];
    header("Location: landing_page.php");
    exit();
}

// Cek apakah jam sudah dibooking oleh user lain
$cek_booking = $conn->prepare("SELECT COUNT(*) FROM informasi_booking WHERE jadwal_booking = ? AND jam = ? AND status = 'dibeli'");
$cek_booking->bind_param("ss", $jadwal_booking, $jam);
$cek_booking->execute();
$cek_booking->bind_result($jumlah);
$cek_booking->fetch();
$cek_booking->close();

if ($jumlah > 0) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Maaf, jam ini sudah dibooking oleh orang lain!'];
    header("Location: landing_page.php");
    exit();
}

// Validasi bukti transfer
if (!isset($_FILES['bukti_transfer']) || $_FILES['bukti_transfer']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Harap unggah bukti transfer!'];
    header("Location: landing_page.php");
    exit();
}

// Proses Upload Bukti Transfer
$target_dir = "uploads/";
$ext = pathinfo($_FILES['bukti_transfer']['name'], PATHINFO_EXTENSION);
$nama_file = time() . "_" . bin2hex(random_bytes(10)) . ".$ext";
$target_file = $target_dir . $nama_file;
$file_type = mime_content_type($_FILES['bukti_transfer']['tmp_name']);
$allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
$file_size = $_FILES['bukti_transfer']['size'];

if ($file_size > 2000000 || !in_array($file_type, $allowed_types)) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Format file tidak valid atau ukuran terlalu besar (Maks 2MB)!'];
    header("Location: landing_page.php");
    exit();
}

if (!move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $target_file)) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Gagal mengunggah bukti transfer. Coba lagi!'];
    header("Location: landing_page.php");
    exit();
}

// Simpan Data Booking
$stmt = $conn->prepare("INSERT INTO informasi_booking 
    (id_user, nama_user, jadwal_booking, jam, status, via_pembayaran, nomor_pembayaran, bukti_transfer, created_at) 
    VALUES (?, ?, ?, ?, 'Pending', ?, ?, ?, NOW())");
$stmt->bind_param("issssss", $id_user, $nama_user, $jadwal_booking, $jam, $via_pembayaran, $nomor_pembayaran, $nama_file);

if ($stmt->execute()) {
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Booking berhasil!'];
} else {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Terjadi kesalahan, coba lagi!'];
}

$stmt->close();
$conn->close();

header("Location: landing_page.php");
exit();

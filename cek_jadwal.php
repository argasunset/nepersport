<?php
include 'koneksi.php'; // Pastikan koneksi sudah benar

// Pastikan tanggal dikirim melalui GET
if (!isset($_GET['tanggal'])) {
    die(json_encode(["error" => "Tanggal tidak ditemukan"]));
}

$tanggal = $_GET['tanggal']; // Ambil tanggal dari request
$hari = date('N', strtotime($tanggal)); // Ambil hari dalam angka (1 = Senin, ..., 7 = Minggu)

// Daftar libur nasional (sesuaikan dengan kebutuhan)
$libur_nasional = ["2025-01-01", "2025-03-29", "2025-05-01", "2025-06-01"];
$isLibur = in_array($tanggal, $libur_nasional);

// Tentukan jam operasional berdasarkan hari
if ($isLibur || $hari == 6 || $hari == 7) {
    // Weekend atau Libur Nasional
    $jam_operasional = [
        "08:00 - 09:00", "09:00 - 10:00", "10:00 - 11:00", "11:00 - 12:00",
        "12:00 - 13:00", "13:00 - 14:00", "14:00 - 15:00", "15:00 - 16:00",
        "16:00 - 17:00", "17:00 - 18:00", "18:00 - 19:00", "19:00 - 20:00",
        "20:00 - 21:00", "21:00 - 22:00", "22:00 - 23:00"
    ];
} else {
    // Weekday (Senin - Jumat)
    $jam_operasional = [
        "17:00 - 18:00", "18:00 - 19:00", "19:00 - 20:00",
        "20:00 - 21:00", "21:00 - 22:00"
    ];
}

// Debug: Tampilkan jam operasional yang dipilih
file_put_contents("debug.log", "Jam Operasional: " . print_r($jam_operasional, true) . "\n", FILE_APPEND);

// Ambil data booking dari database berdasarkan tanggal
$query = "SELECT TRIM(jam) AS jam, LOWER(status) AS status 
          FROM informasi_booking 
          WHERE jadwal_booking = '$tanggal' AND status IN ('booking', 'sold')";

$result = mysqli_query($conn, $query);

// Cek apakah query berhasil
if (!$result) {
    die(json_encode(["error" => "Query error: " . mysqli_error($conn)]));
}

// Simpan data yang sudah dibooking dalam array
$jadwal_terbooking = [];
while ($row = mysqli_fetch_assoc($result)) {
    $jadwal_terbooking[$row['jam']] = $row['status'];
}

// Debug: Tampilkan hasil query
file_put_contents("debug.log", "Jadwal Terbooking: " . print_r($jadwal_terbooking, true) . "\n", FILE_APPEND);

// Format data untuk dikirim ke frontend
$data_jadwal = [];
foreach ($jam_operasional as $jam) {
    if (isset($jadwal_terbooking[$jam])) {
        $status = ($jadwal_terbooking[$jam] == 'sold') ? 'sold' : 'booking';
    } else {
        $status = "kosong";
    }

    $data_jadwal[] = ['jam' => $jam, 'status' => $status];
}

// Debug: Tampilkan data yang akan dikirim
file_put_contents("debug.log", "Data Jadwal Response: " . print_r($data_jadwal, true) . "\n", FILE_APPEND);

// Kirim data dalam format JSON
header('Content-Type: application/json');
echo json_encode($data_jadwal, JSON_PRETTY_PRINT);
?>

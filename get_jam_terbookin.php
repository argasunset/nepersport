<?php
include 'koneksi.php';
$tanggal_booking = $_GET['tanggal_booking'] ?? date('Y-m-d');
$jam_terbooking = [];

$query = $conn->prepare("SELECT jam FROM informasi_booking WHERE jadwal_booking = ?");
$query->bind_param("s", $tanggal_booking);
$query->execute();
$result = $query->get_result();

while ($row = $result->fetch_assoc()) {
  $jam_terbooking[] = $row['jam'];
}

$query->close();
header('Content-Type: application/json');
echo json_encode($jam_terbooking);
?>

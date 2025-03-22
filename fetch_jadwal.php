<?php
include 'koneksi.php';

$query = mysqli_query($conn, "SELECT * FROM jadwal_kalender");
$data = [];

while ($row = mysqli_fetch_assoc($query)) {
  $data[] = [
    'title' => $row['keterangan'],
    'start' => $row['tanggal']
  ];
}

echo json_encode($data);
?>

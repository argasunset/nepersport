<?php
include 'koneksi.php';

$tanggal = $_POST['tanggal'];
$keterangan = $_POST['keterangan'];

$query = mysqli_query($conn, "INSERT INTO jadwal_kalender (tanggal, keterangan, created_at) VALUES ('$tanggal', '$keterangan', NOW())");

if($query){
  echo "Sukses";
} else {
  echo "Gagal: " . mysqli_error($conn);
}
?>

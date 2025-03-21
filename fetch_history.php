<?php
include 'koneksi.php';

$query = "SELECT * FROM history_sewa_lapangan";

// Jika ada pencarian, tambahkan filter query
if (!empty($_POST["query"])) {
    $search = $_POST["query"];
    $query = "SELECT * FROM history_sewa_lapangan 
              WHERE nama_user LIKE '%$search%' 
              OR jadwal_booking LIKE '%$search%' 
              OR jam LIKE '%$search%' 
              OR via_pembayaran LIKE '%$search%' 
              OR status LIKE '%$search%'";
}

$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['nama_user']}</td>
                <td>{$row['jadwal_booking']}</td>
                <td>{$row['jam']}</td>
                <td>{$row['via_pembayaran']}</td>
                <td><span class='badge badge-success'>{$row['status']}</span></td>
                <td>
                    <a href='cetak_booking.php?id={$row['id_history']}' target='_blank' title='Cetak'>
                        <i class='fas fa-print print-icon'></i>
                    </a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6' style='text-align: center; padding: 10px;'>Data tidak ditemukan</td></tr>";
}
?>

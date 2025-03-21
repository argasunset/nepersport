<?php
include 'koneksi.php'; // Sesuaikan koneksi database

if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $query = "SELECT * FROM member WHERE nama_user LIKE '%$search%'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td style='padding: 12px; text-align: center;'>" . $no++ . "</td>"; // Nomor urut tetap rapi
            echo "<td style='padding: 12px; text-align: center;'>" . htmlspecialchars($row['nama_user']) . "</td>"; // Nama User
            echo "<td style='padding: 12px; text-align: center;'>" . (!empty($row['no_telepon_user']) ? htmlspecialchars($row['no_telepon_user']) : '-') . "</td>"; // Nomor Telepon
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='3' style='text-align: center;'>Tidak ada hasil ditemukan</td></tr>";
    }
}
?>

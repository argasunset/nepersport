<?php
include 'koneksi.php';

// Query hanya menampilkan booking yang belum dibatalkan
$query = "SELECT * FROM informasi_booking WHERE status NOT IN ('dibatalkan')";

// Jika ada pencarian, gunakan prepared statement untuk keamanan
if (!empty($_POST["query"])) {
    $search = "%" . $_POST["query"] . "%";
    $query = "SELECT * FROM informasi_booking 
              WHERE (nama_user LIKE ? 
              OR jadwal_booking LIKE ? 
              OR status LIKE ?) 
              AND status NOT IN ('dibatalkan')";
}

// Siapkan statement
$stmt = $conn->prepare($query);

if (!empty($_POST["query"])) {
    $stmt->bind_param("sss", $search, $search, $search);
}

$stmt->execute();
$result = $stmt->get_result();
$no = 1;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Atur warna badge sesuai status
        if ($row['status'] == 'pending') {
            $badgeColor = "#f39c12"; // Orange
        } elseif ($row['status'] == 'selesai') {
            $badgeColor = "#2ecc71"; // Hijau
        } elseif ($row['status'] == 'dibatalkan') {
            $badgeColor = "#e74c3c"; // Merah
        } else {
            $badgeColor = "#95a5a6"; // Abu-abu
        }

        echo "<tr style='border-bottom: 1px solid #ddd;'>
                <td style='padding: 10px;'>{$no}</td>
                <td style='padding: 10px;'>" . htmlspecialchars($row['nama_user']) . "</td>
                <td style='padding: 10px;'>" . htmlspecialchars($row['jadwal_booking']) . "</td>
                <td style='padding: 10px;'>
                    <a href='uploads/" . htmlspecialchars($row['bukti_transfer']) . "' target='_blank' 
                       style='text-decoration: none; color: #3498db; font-weight: bold;'>Lihat Bukti</a>
                </td>
                <td style='padding: 10px;'>
                    <span style='background-color: $badgeColor; color: white; padding: 5px 10px; border-radius: 5px;'>" . ucfirst(htmlspecialchars($row['status'])) . "</span>
                </td>
                <td style='padding: 10px;'>
                    <button class='btn-accept' data-id='{$row['id_booking']}' 
                            style='background-color: #2ecc71; color: white; padding: 6px 10px; border: none; border-radius: 5px; cursor: pointer;'>
                        ✔
                    </button>
                    <button class='btn-reject' data-id='{$row['id_booking']}' 
                            style='background-color: #e74c3c; color: white; padding: 6px 10px; border: none; border-radius: 5px; cursor: pointer;'>
                        ✘
                    </button>
                </td>
              </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='6' style='text-align: center; padding: 10px;'>Data tidak ditemukan</td></tr>";
}

$stmt->close();

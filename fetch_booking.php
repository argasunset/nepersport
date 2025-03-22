<?php
include 'koneksi.php';

// ✅ Handle Accept Request
if (isset($_POST['action']) && $_POST['action'] == 'accept') {
    $id_booking = $_POST['id_booking'];

    // Ambil data booking dari informasi_booking
    $result = $conn->query("SELECT * FROM informasi_booking WHERE id_booking = $id_booking");
    $data = $result->fetch_assoc();

    if ($data) {
        // Pindahkan data ke history_sewa_lapangan
        $insert = $conn->query("INSERT INTO history_sewa_lapangan (id_user, nama_user, jadwal_booking, bukti_transfer, created_at, via_pembayaran, nomor_pembayaran, jam)
            VALUES ('{$data['id_user']}', '{$data['nama_user']}', '{$data['jadwal_booking']}', '{$data['bukti_transfer']}', '{$data['created_at']}', '{$data['via_pembayaran']}', '{$data['nomor_pembayaran']}', '{$data['jam']}')");

        if ($insert) {
            // Hapus dari tabel informasi_booking
            $conn->query("DELETE FROM informasi_booking WHERE id_booking = $id_booking");
            echo 'success';
        } else {
            echo 'insert_failed';
        }
    } else {
        echo 'not_found';
    }
    exit;
}

// ✅ Handle Reject Request
if (isset($_POST['action']) && $_POST['action'] == 'reject') {
    $id_booking = $_POST['id_booking'];
    if ($conn->query("DELETE FROM informasi_booking WHERE id_booking = $id_booking")) {
        echo 'success';
    } else {
        echo 'delete_failed';
    }
    exit;
}

// ✅ Tampilkan data booking seperti sebelumnya
$query = "SELECT * FROM informasi_booking WHERE status NOT IN ('dibatalkan')";

if (!empty($_POST["query"])) {
    $search = "%" . $_POST["query"] . "%";
    $query = "SELECT * FROM informasi_booking 
              WHERE (nama_user LIKE ? 
              OR jadwal_booking LIKE ? 
              OR status LIKE ?) 
              AND status NOT IN ('dibatalkan')";
}

$stmt = $conn->prepare($query);

if (!empty($_POST["query"])) {
    $stmt->bind_param("sss", $search, $search, $search);
}

$stmt->execute();
$result = $stmt->get_result();
$no = 1;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['status'] == 'pending') {
            $badgeColor = "#f39c12";
        } elseif ($row['status'] == 'selesai') {
            $badgeColor = "#2ecc71";
        } elseif ($row['status'] == 'dibatalkan') {
            $badgeColor = "#e74c3c";
        } else {
            $badgeColor = "#95a5a6";
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
?>
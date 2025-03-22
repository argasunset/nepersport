<?php
session_start();
include 'koneksi.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id_booking = $_GET['id'];
    $action = $_GET['action'];

    // Ambil data booking dulu
    $stmt = $conn->prepare("SELECT * FROM informasi_booking WHERE id_booking = ?");
    $stmt->bind_param("i", $id_booking);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if (!$booking) {
        $response['message'] = "Data booking tidak ditemukan.";
        $response['debug'] = $conn->error;
        echo json_encode($response);
        exit();
    }

    if ($action == 'accept') {
        // Pindahkan ke history_sewa_lapangan
        $insert = $conn->prepare("INSERT INTO history_sewa_lapangan (id_user, nama_user, jadwal_booking, bukti_transfer, status, created_at, via_pembayaran, jam)
                                  VALUES (?, ?, ?, ?, 'selesai', ?, ?, ?)");
        $insert->bind_param("issssss", $booking['id_user'], $booking['nama_user'], $booking['jadwal_booking'], $booking['bukti_transfer'], $booking['created_at'], $booking['via_pembayaran'], $booking['jam']);
        
        if ($insert->execute()) {
            // Hapus dari informasi_booking
            $delete = $conn->prepare("DELETE FROM informasi_booking WHERE id_booking = ?");
            $delete->bind_param("i", $id_booking);
            if ($delete->execute()) {
                $response['success'] = true;
                $response['message'] = "Booking berhasil dipindahkan ke history & dihapus dari data booking.";
            } else {
                $response['message'] = "Gagal menghapus data booking setelah memindahkan.";
            }
            $delete->close();
        } else {
            $response['message'] = "Gagal memindahkan data ke history.";
        }
        $insert->close();
    } elseif ($action == 'reject') {
        // Hapus langsung
        $delete = $conn->prepare("DELETE FROM informasi_booking WHERE id_booking = ?");
        $delete->bind_param("i", $id_booking);
        if ($delete->execute()) {
            $response['success'] = true;
            $response['message'] = "Booking berhasil ditolak & dihapus.";
        } else {
            $response['message'] = "Gagal menghapus booking.";
        }
        $delete->close();
    } else {
        $response['message'] = "Aksi tidak valid.";
    }
} else {
    $response['message'] = "Parameter tidak lengkap!";
}

echo json_encode($response);
exit();
?>

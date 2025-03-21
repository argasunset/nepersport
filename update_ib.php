<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id_booking = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'accept') {
        // Update status jadi 'selesai'
        $query = "UPDATE informasi_booking SET status = 'selesai' WHERE id_booking = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $id_booking);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Booking berhasil diselesaikan!";
            } else {
                $response['message'] = "Gagal memperbarui booking!";
            }
            $stmt->close();
        } else {
            $response['message'] = "Query error!";
        }
    } elseif ($action == 'reject') {
        // Update status jadi 'dibatalkan' (tidak dihapus)
        $query = "UPDATE informasi_booking SET status = 'dibatalkan' WHERE id_booking = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $id_booking);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Booking berhasil dibatalkan!";
            } else {
                $response['message'] = "Gagal membatalkan booking!";
            }
            $stmt->close();
        } else {
            $response['message'] = "Query error!";
        }
    } else {
        $response['message'] = "Aksi tidak valid!";
    }
} else {
    $response['message'] = "Parameter tidak lengkap!";
}

echo json_encode($response);
exit();

<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pastikan id dikirim dan berupa angka
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(["status" => "error", "message" => "ID tidak valid"]);
        exit;
    }

    $id = intval($_POST['id']); // Konversi ke integer
    $action = $_POST['action'];

    if ($action == "accept") {
        // Cek apakah ID user ada di tabel user
        $checkUserQuery = "SELECT id_user FROM user WHERE id_user = ?";
        $stmtCheck = $conn->prepare($checkUserQuery);
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        $stmtCheck->close();

        if ($result->num_rows > 0) {
            // Update status user jadi 'member'
            $updateQuery = "UPDATE user SET status = 'member' WHERE id_user = ?";
            $stmtUpdate = $conn->prepare($updateQuery);
            $stmtUpdate->bind_param("i", $id);

            if ($stmtUpdate->execute()) {
                $stmtUpdate->close();

                // Hapus dari tabel member setelah berhasil update
                $deleteQuery = "DELETE FROM member WHERE id_member = ?";
                $stmtDelete = $conn->prepare($deleteQuery);
                $stmtDelete->bind_param("i", $id);

                if ($stmtDelete->execute()) {
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Gagal menghapus data member"]);
                }

                $stmtDelete->close();
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal update status user"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "User tidak ditemukan di tabel user"]);
        }
    } elseif ($action == "reject") {
        // Hapus langsung dari tabel member jika ditolak
        $deleteQuery = "DELETE FROM member WHERE id_member = ?";
        $stmtDelete = $conn->prepare($deleteQuery);
        $stmtDelete->bind_param("i", $id);

        if ($stmtDelete->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menghapus member"]);
        }

        $stmtDelete->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Aksi tidak valid"]);
    }

    $conn->close(); // Tutup koneksi database
}
?>

<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    echo json_encode(["success" => false, "message" => "User belum login"]);
    exit();
}

$id_user = $_SESSION['id_user'];
$folder_upload = "uploads/";

if (!file_exists($folder_upload)) {
    mkdir($folder_upload, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    
    if (!in_array($_FILES['foto']['type'], $allowed_types)) {
        echo json_encode(["success" => false, "message" => "Format file tidak didukung!"]);
        exit();
    }

    $nama_file = "user_" . $id_user . "_" . time() . ".jpg";
    $file_path = $folder_upload . $nama_file;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $file_path)) {
        $stmt = $conn->prepare("UPDATE user SET foto = ? WHERE id_user = ?");
        $stmt->bind_param("si", $file_path, $id_user);
        $stmt->execute();

        $_SESSION['foto'] = $file_path;

        echo json_encode(["success" => true, "filepath" => $file_path]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal mengupload file"]);
    }
}
?>

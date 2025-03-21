<?php
include 'koneksi.php';

if (isset($_POST['id_user']) && isset($_POST['status'])) {
    $id_user = $_POST['id_user'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE user SET status = ? WHERE id_user = ?");
    $stmt->bind_param("si", $status, $id_user);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>

<?php
include 'koneksi.php';

if (isset($_GET['id_member'])) {
    $id_member = $_GET['id_member'];

    $query = "DELETE FROM member WHERE id_member = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_member);
    $stmt->execute();

    header('Location: data_member.php');
    exit();
} else {
    echo "ID member tidak valid.";
}
?>

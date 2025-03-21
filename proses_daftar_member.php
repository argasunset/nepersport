<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_user = $_POST['id_user'];
    $phone_number = $_POST['phone_number'];

    if (empty($id_user) || empty($phone_number)) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
        exit;
    }

    // Cek apakah user sudah menjadi member
    $cekQuery = "SELECT id_member FROM member WHERE id_user = ?";
    $stmtCek = $conn->prepare($cekQuery);
    $stmtCek->bind_param("i", $id_user);
    $stmtCek->execute();
    $resultCek = $stmtCek->get_result();
    $stmtCek->close();

    if ($resultCek->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Anda sudah terdaftar sebagai member"]);
        exit;
    }

    // Simpan ke tabel member
    $query = "INSERT INTO member (id_user, nama_user, no_telepon_user) SELECT id_user, nama_user, ? FROM user WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $phone_number, $id_user);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Berhasil mendaftar sebagai member"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan data"]);
    }

    $stmt->close();
    $conn->close();
}
?>
<script>
document.getElementById("memberForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Mencegah reload

    let formData = new FormData(this);

    fetch("proses_daftar_member.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            location.reload(); // Refresh halaman setelah sukses
        }
    })
    .catch(error => console.error("Error:", error));
});
</script>

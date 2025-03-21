<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan!");
}

$id = $_GET['id'];

// Ambil data booking berdasarkan id_history
$query = $conn->prepare("SELECT * FROM history_sewa_lapangan WHERE id_history = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Booking - NEPERSPORT</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 300px;
            margin: auto;
            text-align: center;
        }
        .struk {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 20px;
        }
        h2 {
            margin: 0;
        }
        .line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .btn-print {
            display: block;
            margin: 20px auto;
            padding: 10px;
            background: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        @media print {
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="struk">
    <h2>NEPERSPORT</h2>
    <p>Jl. Perjuangan, Sunyaragi, Kec. Kesambi,<br>
    Kota Cirebon, Jawa Barat 45132</p>
    <div class="line"></div>
    <p><strong>Nama:</strong> <?= $data['nama_user']; ?></p>
    <p><strong>Tanggal:</strong> <?= $data['jadwal_booking']; ?></p>
    <p><strong>Jam:</strong> <?= $data['jam']; ?></p>
    <p><strong>Pembayaran:</strong> <?= $data['via_pembayaran']; ?></p>
    <p><strong>Status:</strong> <?= $data['status']; ?></p>
    <div class="line"></div>
    <p>Terima kasih telah menggunakan layanan kami!</p>
</div>

<a href="#" class="btn-print" onclick="window.print()">Cetak Struk</a>

</body>
<script>
    window.onload = function() {
        window.print();
        setTimeout(function() {
            window.close();
        }, 500); // Menutup tab setelah 0.5 detik
    };
</script>

</html>

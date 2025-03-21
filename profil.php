<?php
session_start();
include 'koneksi.php';

$logged_in = isset($_SESSION['id_user']);
$nama_user = $logged_in ? $_SESSION['nama_user'] : "Guest";
$email_user = $logged_in ? $_SESSION['email'] : "guest@example.com";
$foto_profil = $logged_in && !empty($_SESSION['foto']) ? $_SESSION['foto'] : "assets/img/default.jpg";
$bookings = [];

if ($logged_in) {
    $id_user = $_SESSION['id_user'];

    $query = $conn->prepare("SELECT * FROM user WHERE id_user = ?");
    $query->bind_param("i", $id_user);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    $query->close();

    // Ambil data booking berdasarkan id_user
    $query_booking = $conn->prepare("SELECT * FROM informasi_booking WHERE nama_user = ? ORDER BY jadwal_booking DESC");
    $query_booking->bind_param("i", $id_user);
    $query_booking->execute();
    $result_booking = $query_booking->get_result();

    while ($row = $result_booking->fetch_assoc()) {
        $bookings[] = $row;
    }

    $query_booking->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;600;700&display=swap');
        body {
            font-family: 'Mulish', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f7f7f7;
            margin: 0;
        }

        .profil-container {
            width: 100%;
            max-width: 600px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }

        .profil-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .profil-foto {
            position: relative;
            display: inline-block;
            /* Biar ukurannya sesuai dengan gambar */
        }

        .profil-foto img {
            display: block;
            width: 100px;
            /* Sesuaikan ukuran */
            height: 100px;
            border-radius: 50%;
            /* Biar bulat */
            object-fit: cover;
        }

        .edit-foto {
            position: absolute;
            bottom: -10px;
            /* Biar keluar dari foto */
            right: -10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        .edit-foto:hover {
            background-color: rgba(0, 0, 0, 0.9);
            transform: scale(1.1);
            /* Efek membesar saat hover */
        }

        .profil-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .profil-info h2 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .profil-info p {
            margin: -12px 0;
        }

        .table-container {
            margin-top: 20px;
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }

        button a {
            color: white;
            text-decoration: none;
        }

        button:hover {
            background-color: #d32f2f;
        }

        .foto-edit-input {
            display: none;
        }
    </style>
</head>
<body>
    <div class="profil-container">
        <div class="profil-header">
            <div class="profil-foto">
                <img id="profilGambar" src="<?php echo $foto_profil; ?>" alt="Profile Picture">
                <?php if ($logged_in) { ?>
                    <label for="foto-edit" class="edit-foto"><i class="fa fa-pencil"></i></label>
                    <input type="file" id="foto-edit" class="foto-edit-input" accept="image/*">
                <?php } ?>
            </div>
            <div class="profil-info">
                <h3><?php echo $nama_user; ?></h3>
                <p><?php echo $email_user; ?></p>
            </div>
        </div>

        <!-- Tabel Booking (Hanya tampil kalau user login) -->
        <?php if ($logged_in) { ?>
            <div class="table-container">
                <h3>Detail Booking</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Booking</th>
                            <th>Jam</th>
                            <th>Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)) { ?>
                            <tr>
                                <td colspan="4" style="background-color: #f2f2f2; text-align: center;">Tidak Ada Data Booking</td>
                            </tr>
                        <?php } else {
                            $no = 1;
                            foreach ($bookings as $booking) { ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $booking['tanggal_booking']; ?></td>
                                    <td><?php echo $booking['jam']; ?></td>
                                    <td><?php echo ucfirst($booking['status_pembayaran']); ?></td>
                                </tr>
                            <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

        <?php if ($logged_in) { ?>
            <button id="logoutBtn">Logout</button>
        <?php } else { ?>
            <button onclick="window.location.href='login.php'">Login</button>
        <?php } ?>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            <?php if ($logged_in) { ?>
                document.getElementById('logoutBtn').addEventListener('click', function (event) {
                    event.preventDefault();

                    Swal.fire({
                        title: "Yakin ingin logout?",
                        text: "Anda harus login kembali untuk masuk!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Logout",
                        cancelButtonText: "Cancel"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "logout.php";
                        }
                    });
                });

                document.getElementById("foto-edit").addEventListener("change", function () {
                    let file = this.files[0];
                    if (file) {
                        let formData = new FormData();
                        formData.append("foto", file);

                        fetch("upload_foto.php", {
                            method: "POST",
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById("profilGambar").src = data.filepath + "?" + new Date().getTime();
                            } else {
                                Swal.fire("Error", data.message, "error");
                            }
                        })
                        .catch(error => console.error("Error:", error));
                    }
                });
            <?php } ?>
        </script>
    </div>
</body>
</html>

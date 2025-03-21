<?php
include 'koneksi.php'; // Sesuaikan dengan koneksi database kamu

if (isset($_POST['search'])) {
    $search = $_POST['search'];

    // Tambahkan filter untuk hanya menampilkan user dengan role "User"
    $query = "SELECT id_user, nama_user, email, no_telepon_user, alamat_user, role_user, status 
              FROM user 
              WHERE role_user = 'User' AND 
              (nama_user LIKE '%$search%' 
              OR email LIKE '%$search%' 
              OR no_telepon_user LIKE '%$search%' 
              OR alamat_user LIKE '%$search%')";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query Error: " . mysqli_error($conn)); // Debugging error jika ada masalah
    }

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td class='table-data'>{$row['nama_user']}</td>
                    <td class='table-data'>{$row['email']}</td>
                    <td class='table-data'>{$row['no_telepon_user']}</td>
                    <td class='table-data'>{$row['alamat_user']}</td>
                    <td class='table-data'>{$row['role_user']}</td>
                    <td class='table-data'>
                        <button class='status-btn " . ($row['status'] == 'Member' ? 'btn-member' : 'btn-nonmember') . "' 
                            data-id='{$row['id_user']}' 
                            data-status='{$row['status']}'>
                            {$row['status']}
                        </button>
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='6' class='text-center'>Data tidak ditemukan</td></tr>";
    }
}
?>

<style>
    /* Biar semua teks di dalam tabel sejajar ke tengah */
    td,
    th {
        text-align: center;
        vertical-align: middle;
        padding: 8px;
    }

    /* Pastikan tombol sejajar dengan teks lainnya */
    .status-btn {
        padding: 5px 10px;
        font-size: 14px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        width: 100px;
        /* Biar ukuran tombol konsisten */
        display: inline-block;
    }

    /* Warna tombol sesuai status */
    .btn-member {
        background-color: green;
        color: white;
    }

    .btn-nonmember {
        background-color: red;
        color: white;
    }
</style>
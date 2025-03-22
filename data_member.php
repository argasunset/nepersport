<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi sudah benar

// Periksa apakah user sudah login
if (!isset($_SESSION['nama_user'])) {
  echo "Session tidak tersedia! Silakan login kembali.";
  exit();
}

// Ambil nama user dari session
$username = $_SESSION['nama_user'];

// Perbaikan query untuk mengambil data user
$stmt = $conn->prepare("SELECT * FROM user WHERE nama_user = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah user ditemukan
if ($result->num_rows > 0) {
  $user = $result->fetch_assoc();
  $email = $user['email'];  // Ambil email dari database
  $nama = $user['nama_user']; // Ambil nama user
} else {
  echo "User tidak ditemukan. Debug: " . htmlspecialchars($username);
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Data Member</title>
  <meta
    content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
    name="viewport" />
  <link
    rel="icon"
    href="assets/img/neperking.png"
    type="image/x-icon" />

  <!-- Fonts and icons -->
  <script src="assets/js/plugin/webfont/webfont.min.js"></script>
  <script>
    WebFont.load({
      google: {
        families: ["Public Sans:300,400,500,600,700"]
      },
      custom: {
        families: [
          "Font Awesome 5 Solid",
          "Font Awesome 5 Regular",
          "Font Awesome 5 Brands",
          "simple-line-icons",
        ],
        urls: ["assets/css/fonts.min.css"],
      },
      active: function() {
        sessionStorage.fonts = true;
      },
    });
  </script>

  <!-- CSS Files -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/css/plugins.min.css" />
  <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link rel="stylesheet" href="assets/css/demo.css" />
</head>

<body>
  <!-- Ganti dengan ikon Font Awesome -->
  <div id="spinner" class="spinner-container">
    <i class="fas fa-spinner fa-spin" style="font-size: 40px; color: #3498db;"></i>
  </div>

  <!-- Tambahkan Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    /* Hapus background kabut */
    .spinner-container {
      position: fixed;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      pointer-events: none;
      /* Supaya tidak mengganggu klik */
      background: transparent;
      /* Hapus background */
    }

    /* Spinner */
    .spinner {
      border: 5px solid rgba(0, 0, 0, 0.1);
      border-top: 5px solid #3498db;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }
  </style>
  </style>
  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
      <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
          <a href="dashbord_admin.php" class="logo">
            <img
              src="assets/img/neperspot.svg"
              alt="navbar brand"
              class="navbar-brand"
              height="20" />
          </a>
          <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
              <i class="gg-menu-right"></i>
            </button>
            <button class="btn btn-toggle sidenav-toggler">
              <i class="gg-menu-left"></i>
            </button>
          </div>
          <button class="topbar-toggler more">
            <i class="gg-more-vertical-alt"></i>
          </button>
        </div>
        <!-- End Logo Header -->
      </div>
      <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
          <ul class="nav nav-secondary">
            <li class="nav-item active">
            <li class="nav-item">
              <a href="dashbord_admin.php">
                <i class="fas fa-home"></i>
                <p>Dashbord</p>
              </a>
            </li>
            <li class="nav-section">
              <span class="sidebar-mini-icon">
                <i class="fa fa-ellipsis-h"></i>
              </span>
              <h4 class="text-section">DATA-DATA</h4>
            </li>
            <li class="nav-item">
              <a href="data_customer.php">
                <i class="fas fa-users"></i>
                <p>Data Customer</p>
                <span class="badge badge-secondary">1</span>
              </a>
            </li>
            <li class="nav-item active">
              <a href="data_member.php">
                <i class="fas fa-address-card"></i>
                <p>Data Daftar Member</p>
                <span class="badge badge-secondary">1</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="informasi_booking.php">
                <i class="fas fa-desktop"></i>
                <p>Informasi Booking</p>
                <span class="badge badge-secondary">1</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="history_sewa_lapangan.php">
                <i class="fas fa-file"></i>
                <p>History Sewa Lapangan</p>
                <span class="badge badge-secondary">1</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- End Sidebar -->

    <div class="main-panel">
      <div class="main-header">
        <div class="main-header-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
              <img
                src="assets/img/kaiadmin/logo_light.svg"
                alt="navbar brand"
                class="navbar-brand"
                height="20" />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <!-- Navbar Header -->
        <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
          <div class="container-fluid">
            <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
              <div class="input-group">
                <div class="input-group-prepend">
                  <button type="button" id="btn-search" class="btn btn-search pe-1">
                    <i class="fa fa-search search-icon"></i>
                  </button>
                </div>
                <input type="text" id="search" placeholder="Search ..." class="form-control">
              </div>
            </nav>

            <!-- Tambahkan UL buat navbar menu -->
            <ul class="navbar-nav ms-auto align-items-center">
              <li class="nav-item topbar-user dropdown hidden-caret">
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                  <div class="avatar-sm">
                    <img src="assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle" />
                  </div>
                  <span class="profile-username">
                    <span class="op-7">Hi,</span>
                    <span class="fw-bold"><?php echo htmlspecialchars($username); ?>!</span>
                  </span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn">
                  <li class="dropdown-user-scroll scrollbar-outer">
                    <div class="user-box">
                      <div class="avatar-lg">
                        <img src="assets/img/profile.jpg" alt="image profile" class="avatar-img rounded" />
                      </div>
                      <div class="u-text">
                        <h4><?= htmlspecialchars($nama); ?></h4>
                        <p class="text-muted"><?= htmlspecialchars($email); ?></p>
                        <a href="profile.php" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li><a class="dropdown-item" href="login.php">Logout</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </div>

      <div class="container">
        <div class="page-inner">
          <h2 class="title">Data Member</h2>

          <div class="table-container">
            <table id="memberTable">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Nomor Telepon</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="memberTableBody">
                <?php
                include 'koneksi.php';

                if ($conn->connect_error) {
                  echo "<tr><td colspan='4'>Gagal Koneksi Database</td></tr>";
                } else {
                  $query = "SELECT id_member, nama_user, no_telepon_user FROM member";
                  $stmt = $conn->prepare($query);
                  $stmt->execute();
                  $result = $stmt->get_result();
                  $no = 1;

                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . $no . "</td>";
                      echo "<td>" . htmlspecialchars($row['nama_user']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['no_telepon_user']) . "</td>";
                      echo "<td>
                              <button class='btn-icon-hapus' onclick='showHapusModal(" . $row['id_member'] . ", \"" . addslashes($row['nama_user']) . "\")'>
                              <i class='fas fa-trash'></i>
                            </button>
                            </td>";
                      echo "</tr>";
                      $no++;
                    }
                  } else {
                    echo "<tr><td colspan='4'>Tidak ada member</td></tr>";
                  }
                  $stmt->close();
                }
                $conn->close();
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <style>
        .btn-icon-hapus {
          background-color: #e74c3c;
          border: none;
          color: white;
          padding: 8px 10px;
          border-radius: 50%;
          cursor: pointer;
        }

        .btn-icon-hapus i {
          font-size: 14px;
        }

        .modal {
          display: none;
          position: fixed;
          z-index: 999;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
          background-color: #fff;
          margin: 15% auto;
          padding: 20px;
          width: 400px;
          border-radius: 10px;
          text-align: center;
        }

        .modal-buttons button {
          margin: 10px;
          padding: 10px 20px;
          cursor: pointer;
        }

        .btn-danger {
          background-color: #e74c3c;
          color: white;
          border: none;
          border-radius: 5px;
        }

        .btn-cancel {
          background-color: #bdc3c7;
          border: none;
          color: white;
          border-radius: 5px;
        }
      </style>
      <script>
        let idMemberHapus = null;

        function showHapusModal(id, nama) {
          idMemberHapus = id;
          document.getElementById('namaUserHapus').innerText = nama;
          document.getElementById('hapusModal').style.display = 'block';
        }

        function closeHapusModal() {
          document.getElementById('hapusModal').style.display = 'none';
        }

        function hapusMemberConfirm() {
          if (idMemberHapus !== null) {
            window.location.href = 'hapus_member.php?id_member=' + idMemberHapus;
          }
        }

        // Agar modal bisa ditutup jika klik di luar modal
        window.onclick = function(event) {
          const modal = document.getElementById('hapusModal');
          if (event.target == modal) {
            closeHapusModal();
          }
        }
      </script>

      <!-- Modal Hapus -->
      <div id="hapusModal" class="modal">
        <div class="modal-content">
          <h3>Yakin ingin menghapus <span id="namaUserHapus"></span> sebagai member?</h3>
          <div class="modal-buttons">
            <button onclick="hapusMemberConfirm()" class="btn-danger">Ya, Hapus</button>
            <button onclick="closeHapusModal()" class="btn-cancel">Batal</button>
          </div>
        </div>
      </div>

      <script>
        function hapusMember(id) {
          if (confirm("Apakah Anda yakin ingin menghapus member ini?")) {
            fetch('hapus_member.php?id=' + id, {
                method: 'GET'
              })
              .then(response => response.text())
              .then(data => {
                if (data.trim() === "sukses") {
                  alert("Member berhasil dihapus!");
                  document.getElementById("row_" + id).remove(); // Hapus baris dari tabel
                } else {
                  alert("Gagal menghapus member.");
                }
              })
              .catch(error => console.error("Error:", error));
          }
        }
      </script>


      <!-- CSS (Disesuaikan dengan Halaman Lain) -->
      <style>
        body {
          font-family: 'Poppins', sans-serif;
          background-color: #f8f9fa;
          color: #333;
          margin: 0;
          padding: 0;
        }

        .container {
          width: 90%;
          max-width: 1000px;
          margin: 30px auto;
          padding: 20px;
        }

        .page-inner {
          background: #fff;
          padding: 20px;
          border-radius: 8px;
          box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .title {
          text-align: center;
          color: #1c1c1c;
          font-weight: 600;
          font-size: 24px;
          text-transform: uppercase;
          margin-bottom: 20px;
        }

        .table-container {
          overflow-x: auto;
          /* Membantu agar tabel tetap rapi di layar kecil */
        }

        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 10px;
        }

        thead {
          background-color: #1a1a2e;
          color: white;
        }

        th,
        td {
          padding: 12px;
          border: 1px solid #ddd;
          text-align: center;
        }

        tr:nth-child(even) {
          background-color: #f4f4f4;
        }

        tr:hover {
          background-color: #e9ecef;
        }

        .no-data {
          text-align: center;
          font-weight: bold;
          color: #888;
        }
      </style>

      <!-- Script (Jika Dibutuhkan) -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script>
        $(document).ready(function() {
          let defaultData = $("#memberTable tbody").html(); // Simpan data awal

          // Fungsi pencarian member
          function searchMember(query) {
            $.ajax({
              url: "proses_search_member.php",
              type: "POST",
              data: {
                search: query
              },
              success: function(data) {
                $("#memberTable tbody").html(data);
              },
              error: function(xhr, status, error) {
                console.error("Error AJAX:", error);
              }
            });
          }

          // Event ketika mengetik di search bar
          $("#searchNavbar").on("keyup", function() {
            let query = $(this).val().trim();
            if (query === "") {
              $("#memberTable tbody").html(defaultData); // Kembalikan ke kondisi awal tanpa refresh
            } else {
              searchMember(query);
            }
          });
        });
      </script>

      <footer class="footer">
        <div class="container-fluid d-flex justify-content-between">
          <nav class="pull-left">
            <ul class="nav">
              <li class="nav-item">
                <a class="nav-link" href="http://www.themekita.com">
                  NeperSport
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#"> Help </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#"> Licenses </a>
              </li>
            </ul>
          </nav>
          <div class="copyright">
            2025, made with <i class="fa fa-heart heart text-danger"></i> by
            <a href="http://www.themekita.com">SMKN 1 KOTA CIREBON</a>
          </div>
          <div>
            Distributed by
            <a target="_blank" href="https://themewagon.com/">SMKN 1 KOTA CIREBON</a>.
          </div>
        </div>
      </footer>

    </div>

  </div>
  <!--   Core JS Files   -->
  <script src="assets/js/core/jquery-3.7.1.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>

  <!-- jQuery Scrollbar -->
  <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

  <!-- Chart JS -->
  <script src="assets/js/plugin/chart.js/chart.min.js"></script>

  <!-- jQuery Sparkline -->
  <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

  <!-- Chart Circle -->
  <script src="assets/js/plugin/chart-circle/circles.min.js"></script>

  <!-- Datatables -->
  <script src="assets/js/plugin/datatables/datatables.min.js"></script>

  <!-- jQuery Vector Maps -->
  <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
  <script src="assets/js/plugin/jsvectormap/world.js"></script>

  <!-- Sweet Alert -->
  <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

  <!-- Kaiadmin JS -->
  <script src="assets/js/kaiadmin.min.js"></script>

  <!-- Kaiadmin DEMO methods, don't include it in your project! -->
  <script src="assets/js/setting-demo.js"></script>
  <script src="assets/js/demo.js"></script>
  <script>
    $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#177dff",
      fillColor: "rgba(23, 125, 255, 0.14)",
    });

    $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#f3545d",
      fillColor: "rgba(243, 84, 93, .14)",
    });

    $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
      type: "line",
      height: "70",
      width: "100%",
      lineWidth: "2",
      lineColor: "#ffa534",
      fillColor: "rgba(255, 165, 52, .14)",
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Tampilkan spinner
      document.getElementById("spinner").style.display = "flex";

      // Sembunyikan spinner setelah halaman selesai dimuat + delay 1 detik
      window.onload = function() {
        setTimeout(function() {
          document.getElementById("spinner").style.display = "none";
        }, 1000); // 1000 ms = 2 detik
      };
    });
  </script>
</body>

</html>
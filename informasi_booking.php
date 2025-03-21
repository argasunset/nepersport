<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi database

// Cek apakah user sudah login
if (!isset($_SESSION['nama_user'])) {
  header("Location: login.php");
  exit();
}

// Ambil nama user dari session
$username = $_SESSION['nama_user'];

// Query untuk mengambil data user
$stmt = $conn->prepare("SELECT * FROM user WHERE nama_user = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Jika user ditemukan
if ($result->num_rows > 0) {
  $user = $result->fetch_assoc();
  $email = $user['email']; // Asumsikan kolom email di tabel adalah 'email_user'
  $nama = $user['nama_user'];   // Nama user
} else {
  echo "User tidak ditemukan.";
  exit();
}

if (isset($_SESSION['success'])) {
  echo "<script>
      Swal.fire({
          icon: 'success',
          title: '{$_SESSION['success']}',
          showConfirmButton: false,
          timer: 2000
      });
  </script>";
  unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
  echo "<script>
      Swal.fire({
          icon: 'error',
          title: '{$_SESSION['error']}',
          showConfirmButton: false,
          timer: 2000
      });
  </script>";
  unset($_SESSION['error']);
}

// Cek apakah query berjalan
$query_booking = "SELECT * FROM informasi_booking";
$result_booking = $conn->query($query_booking);

// Cek apakah ada data di tabel informasi_booking
if (!$result_booking) {
  die("Error pada query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Informasi Booking NeperSport</title>
  <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
  <link rel="icon" href="assets/img/neperking.png" type="image/x-icon" />

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
  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
      <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
          <a href="dashbord_admin.php" class="logo">
            <img src="assets/img/neperspot.svg" alt="navbar brand" class="navbar-brand" height="20" />
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
            <li class="nav-item">
              <a href="data_member.php">
                <i class="fas fa-address-card"></i>
                <p>Data Daftar Member</p>
                <span class="badge badge-secondary">1</span>
              </a>
            </li>
            <li class="nav-item active">
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
              <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
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
                  <li><a class="dropdown-item" href="#">My Profile</a></li>
                  <li><a class="dropdown-item" href="#">My Balance</a></li>
                  <li><a class="dropdown-item" href="#">Inbox</a></li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li><a class="dropdown-item" href="#">Account Setting</a></li>
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
          <!-- Judul -->
          <h2
            style="text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 20px; text-transform: uppercase;">
            INFORMASI BOOKING
          </h2>

          <div style="width: 100%; overflow-x: auto;">
            <div id="loading" style="text-align: center; display: none;">
              <img src="assets/loading.gif" width="50">
            </div>
            <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
              <thead>
                <tr style="background-color: #1a1a2e; color: white;">
                  <th style="padding: 12px;">NO</th>
                  <th style="padding: 12px;">NAMA USER</th>
                  <th style="padding: 12px;">JADWAL</th>
                  <th style="padding: 12px;">BUKTI TRANSFER</th>
                  <th style="padding: 12px;">STATUS</th>
                  <th style="padding: 12px;">AKSI</th>
                </tr>
              </thead>
              <tbody id="data-booking">
                <!-- Data booking akan dimuat di sini melalui AJAX -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        $(document).ready(function() {
          // Fungsi untuk memuat data booking
          function loadData(query = '') {
            $.ajax({
              url: "fetch_booking.php",
              method: "POST",
              data: {
                query: query
              },
              success: function(data) {
                $("#data-booking").html(data);
              }
            });
          }

          // Load data saat halaman dibuka
          loadData();

          // Pencarian real-time
          $("#search").keyup(function() {
            var searchText = $(this).val();
            loadData(searchText);
          });

          // Fungsi untuk aksi accept / reject
          function konfirmasiAction(action, id) {
            Swal.fire({
              title: action === 'accept' ? "Terima Booking?" : "Tolak Booking?",
              text: action === 'accept' ? "Booking ini akan diterima." : "Booking ini akan ditolak dan dihapus.",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: action === 'accept' ? "#28a745" : "#d33",
              cancelButtonColor: "#6c757d",
              confirmButtonText: action === 'accept' ? "Ya, Terima" : "Ya, Tolak"
            }).then((result) => {
              if (result.isConfirmed) {
                $.ajax({
                  url: "update_ib.php",
                  method: "GET",
                  dataType: "json",
                  data: {
                    id: id,
                    action: action
                  },
                  success: function(response) {
                    Swal.fire({
                      title: response.success ? "Berhasil!" : "Gagal!",
                      text: response.message,
                      icon: response.success ? "success" : "error",
                      timer: 2000,
                      showConfirmButton: false
                    });

                    // Reload data setelah update
                    if (response.success) {
                      setTimeout(() => {
                        loadData();
                      }, 2000);
                    }
                  },
                  error: function() {
                    Swal.fire("Error!", "Terjadi kesalahan saat memproses data.", "error");
                  }
                });
              }
            });
          }

          // Event listener tombol accept & reject
          $(document).on("click", ".btn-accept", function() {
            var id = $(this).data("id");
            konfirmasiAction("accept", id);
          });

          $(document).on("click", ".btn-reject", function() {
            var id = $(this).data("id");
            konfirmasiAction("reject", id);
          });
        });
      </script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

  <!-- Bootstrap Notify -->
  <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

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
  <!-- Spinner -->
  <div class="spinner" id="loadingSpinner">
    <style>
      /* Spinner Loading */
      .spinner {
        display: none;
        /* Default: tersembunyi */
        position: fixed;
        z-index: 9999;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 80px;
        height: 80px;
        border: 8px solid rgba(0, 0, 0, 0.1);
        border-left-color: #3498db;
        border-radius: 50%;
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

    <script>
      $(document).ready(function() {
        // Fungsi untuk memuat data booking
        function loadData(query = '') {
          $.ajax({
            url: "fetch_booking.php",
            method: "POST",
            data: {
              query: query
            },
            success: function(data) {
              $("#data-booking").html(data);
            }
          });
        }

        // Panggil loadData saat halaman pertama kali dibuka
        loadData();

        // Event untuk pencarian real-time
        $("#search").keyup(function() {
          var searchText = $(this).val();
          loadData(searchText);
        });

        // Event saat klik tombol "Terima" atau "Tolak"
        $(document).on("click", ".btn-accept, .btn-reject", function() {
          let id = $(this).data("id");
          let action = $(this).hasClass("btn-accept") ? "accept" : "reject";
          let confirmText = action === "accept" ? "Terima Booking?" : "Tolak Booking?";
          let confirmColor = action === "accept" ? "#28a745" : "#d33";

          Swal.fire({
            title: confirmText,
            text: action === "accept" ? "Booking ini akan diterima." : "Booking ini akan ditolak.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: "#6c757d",
            confirmButtonText: action === "accept" ? "Ya, Terima" : "Ya, Tolak"
          }).then((result) => {
            if (result.isConfirmed) {
              // Tampilkan spinner
              $("#loadingSpinner").fadeIn();

              // Kirim request ke update_ib.php menggunakan AJAX
              $.ajax({
                url: "update_ib.php",
                method: "GET",
                data: {
                  id: id,
                  action: action
                },
                success: function(response) {
                  // Sembunyikan spinner
                  $("#loadingSpinner").fadeOut();

                  // Tampilkan alert sukses
                  Swal.fire("Berhasil!", "Status booking telah diperbarui.", "success");

                  // Muat ulang data
                  loadData();
                },
                error: function() {
                  $("#loadingSpinner").fadeOut();
                  Swal.fire("Gagal!", "Terjadi kesalahan, coba lagi.", "error");
                }
              });
            }
          });
        });
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
  </div>
</body>

</html>
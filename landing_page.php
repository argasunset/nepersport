<?php
session_start();
include 'koneksi.php';

$id_user = $_SESSION['id_user'] ?? null; // Ambil id_user jika login
$is_member = false; // Default bukan member

$nama_user = "";
$email_user = "";
$no_telepon_user = "";

// Jika user sudah login, ambil data user dan cek keanggotaan
if ($id_user) {
  $query = $conn->prepare("SELECT u.nama_user, u.email, u.no_telepon_user, m.id_member 
                             FROM user u 
                             LEFT JOIN member m ON u.id_user = m.id_user 
                             WHERE u.id_user = ?");
  $query->bind_param("i", $id_user);
  $query->execute();
  $result = $query->get_result();
  $user = $result->fetch_assoc();
  $query->close();

  if ($user) {
    $nama_user = $user['nama_user'];
    $email_user = $user['email'];
    $no_telepon_user = $user['no_telepon_user'];
    $is_member = !is_null($user['id_member']); // Jika id_member ada, berarti user adalah member
  }
}

// **PROSES PENDAFTARAN MEMBER**
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!$id_user) {
    $_SESSION['alert'] = ["type" => "error", "message" => "Anda harus login terlebih dahulu!"];
    header("Location: login.php");
    exit();
  }

  // Cek apakah user sudah jadi member
  if ($is_member) {
    $_SESSION['alert'] = ["type" => "info", "message" => "Anda sudah terdaftar sebagai member!"];
    header("Location: landing_page.php");
    exit();
  }

  // Insert data ke tabel member
  $phone_number = trim(htmlspecialchars($_POST['phone_number'])); // Sanitasi input

  $stmt = $conn->prepare("INSERT INTO member (id_user, nama_user, email_user, no_telepon_user) 
                            VALUES (?, ?, ?, ?)");
  $stmt->bind_param("isss", $id_user, $nama_user, $email_user, $phone_number);

  if ($stmt->execute()) {
    $_SESSION['alert'] = ["type" => "success", "message" => "Selamat! Anda telah terdaftar sebagai member."];
  } else {
    $_SESSION['alert'] = ["type" => "error", "message" => "Terjadi kesalahan, silakan coba lagi!"];
  }

  $stmt->close();
  header("Location: landing_page.php");
  exit();
}

// **AMBIL JAM YANG SUDAH DIBOOKING**
$tanggal_booking = date("Y-m-d"); // Default ke hari ini
$jam_terbooking = [];

$query = $conn->prepare("SELECT jam FROM informasi_booking WHERE jadwal_booking = ?");
$query->bind_param("s", $tanggal_booking);
$query->execute();
$result = $query->get_result();

while ($row = $result->fetch_assoc()) {
  $jam_terbooking[] = $row['jam'];
}

$query->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Neper Sport</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/neperking.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.9/dist/sweetalert2.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      font-family: 'Mulish', sans-serif;
    }

    /* ========== Modal Member ========== */
    .modal-content-member {
      border-radius: 10px;
      overflow: hidden;
    }

    .modal-header-member {
      background: #FFC400;
      color: white;
    }

    .modal-body-member {
      display: flex;
      flex-direction: row;
      gap: 15px;
      padding: 20px;
    }

    .left-section-member {
      width: 60%;
      padding: 20px;
    }

    .left-section-member h2 {
      font-size: 18px;
      color: #333;
      margin-bottom: 10px;
    }

    .left-section-member p {
      font-size: 14px;
      color: #666;
    }

    .input-field-member {
      width: 100%;
      padding: 10px;
      margin: 5px 0;
      border: 1px solid #ccc;
      border-radius: 5px;

    }

    .submit-btn-member {
      width: 100%;
      background: #FFC400;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
      transition: 0.3s;
    }

    .submit-btn-member:hover {
      background: #FFC400;
    }

    .trusted-member,
    .terms-member {
      font-size: 12px;
      color: #777;
      margin-top: 10px;
    }

    .terms-member a {
      color: #FFC400;
      text-decoration: none;
    }

    .terms-member a:hover {
      text-decoration: underline;
    }

    .right-section-member {
      width: 40%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .right-section-member img {
      width: 100%;
      max-width: 1000px;
      aspect-ratio: 9 / 16;
      object-fit: cover;
      /* Pastikan gambar tetap terlihat bagus */
      border-radius: 10px;
      height: 400px;
    }


    .input-container {
      position: relative;
      width: 100%;
      margin-bottom: 15px;
      /* Memberikan jarak antar input */
    }

    .input-field-member {
      width: 100%;
      padding-left: 35px;
      /* Memberikan ruang untuk ikon */
      padding-right: 10px;
      padding-top: 10px;
      padding-bottom: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .input-container i {
      position: absolute;
      top: 50%;
      left: 10px;
      /* Posisi ikon di kiri */
      transform: translateY(-50%);
      font-size: 18px;
      /* Ukuran ikon */
      color: #888;
      /* Warna ikon */
    }

    .alert {
      text-align: center;
      border-radius: 12px;
      padding: 40px;
      background-color: #e9f7e9;
      border: 2px solid #4CAF50;
      color: #4CAF50;
      font-size: 18px;
    }

    .alert i {
      font-size: 80px;
      color: #4CAF50;
    }

    .alert h4 {
      margin-top: 15px;
      font-size: 22px;
      font-weight: bold;
    }
  </style>

  <!-- =======================================================
  * Template Name: QuickStart
  * Template URL: https://bootstrapmade.com/quickstart-bootstrap-startup-website-template/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.html" class="logo d-flex align-items-center me-auto">
        <img src="assets/img/neperking.png" alt="">
        <h1 class="sitename">NeperSport</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="login.php">Sign Up</a></li>
          <li><a href="profil.php"><i class="bi bi-person-circle" style="font-size: medium;"></i></a></li>
          <li>
            <a href="#" data-toggle="modal" data-target="#memberModal">
              <i class="bi bi-person-vcard" style="font-size: larger"></i>
            </a>
          </li>

        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <div class="modal fade" id="memberModal" tabindex="-1" aria-labelledby="memberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content modal-content-member">
        <div class="modal-header modal-header-member">
          <h5 class="modal-title" id="memberModalLabel"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body-member">
          <div class="left-section-member">
            <h2>Gabung sebagai member dan dapatkan keuntungan eksklusif!</h2>
            <p>Data Anda aman bersama kami!</p>
            <form method="POST" id="memberForm">
              <input type="hidden" name="id_user" value="<?= $id_user; ?>"> <!-- Tambahkan ID User -->
              <div class="input-container">
                <i class="bi bi-person"></i>
                <input type="text" value="<?= $nama_user; ?>" class="input-field-member" readonly>
              </div>
              <div class="input-container">
                <i class="bi bi-envelope"></i>
                <input type="email" value="<?= $email_user; ?>" class="input-field-member" readonly>
              </div>
              <div class="input-container">
                <i class="bi bi-telephone"></i>
                <input type="tel" name="phone_number" placeholder="Masukkan Nomor Telepon" class="input-field-member" required>
              </div>
              <button type="submit" class="submit-btn-member">Daftar Member</button>
            </form>
            <p class="terms-member">Dengan mendaftar, Anda menyetujui <a href="#">Syarat & Ketentuan</a> serta <a href="#">Kebijakan Privasi</a></p>
          </div>
          <div class="right-section-member">
            <img src="assets/img/bola.jpg" alt="Illustration">
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Cek apakah user sudah login sebelum mengirim form
    document.getElementById("memberForm").addEventListener("submit", function(event) {
      <?php if (!$id_user): ?>
        event.preventDefault();
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Anda harus login terlebih dahulu untuk mendaftar sebagai member!',
          confirmButtonText: 'Login Sekarang'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = 'login.php';
          }
        });
      <?php endif; ?>
    });

    // Tampilkan alert jika ada pesan dari session
    <?php if (isset($_SESSION['alert'])): ?>
      Swal.fire({
        icon: '<?= $_SESSION['alert']['type']; ?>',
        title: '<?= $_SESSION['alert']['type'] == 'success' ? 'Berhasil!' : 'Oops...'; ?>',
        text: '<?= $_SESSION['alert']['message']; ?>',
        timer: 2000,
        showConfirmButton: false
      });
      <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>
  </script>

  <!-- Modal Jadwal -->
  <div class="modal fade" id="jadwalModal" tabindex="-1" role="dialog" aria-labelledby="jadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="jadwalModalLabel">Jadwal Lapangan Futsal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Button Pilih Tanggal -->
          <div class="d-flex justify-content-between mb-3">
            <input type="date" id="tanggal-pilih" class="form-control w-25" value="<?= date('Y-m-d'); ?>">
            <button class="btn" style="background-color: #FFC400; color: white;" onclick="loadJadwal()">Tampilkan Jadwal</button>
          </div>

          <!-- Spinner Loading -->
          <div id="loading" style="display: none; text-align: center; font-size: 18px;">
            <p><strong>Memuat data...</strong></p>
          </div>

          <!-- Tabel Jadwal -->
          <div class="table-responsive">
            <table class="table table-bordered text-center">
              <thead class="thead-dark">
                <tr>
                  <th>Jam</th>
                  <th>Status Lapangan</th>
                </tr>
              </thead>
              <tbody id="jadwal-body">
                <tr>
                  <td colspan="2">Silakan pilih tanggal untuk melihat jadwal.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function loadJadwal() {
      const tanggal = document.getElementById('tanggal-pilih').value;
      const jadwalBody = document.getElementById('jadwal-body');
      const loading = document.getElementById('loading');

      if (!tanggal) {
        alert("Pilih tanggal terlebih dahulu!");
        return;
      }

      jadwalBody.innerHTML = '<tr><td colspan="2">Memuat data...</td></tr>';
      loading.style.display = "block";

      fetch(`cek_jadwal.php?tanggal=${tanggal}`)
        .then(response => response.json())
        .then(data => {
          jadwalBody.innerHTML = '';
          loading.style.display = "none";

          data.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>${item.status === 'sold' ? `<s>${item.jam}</s>` : item.jam}</td>
            <td>${getStatusBadge(item.status)}</td>
          `;
            jadwalBody.appendChild(row);
          });

          alert(`Jadwal untuk tanggal ${tanggal} telah dimuat!`);
        })
        .catch(error => {
          console.error("Error mengambil data:", error);
          jadwalBody.innerHTML = '<tr><td colspan="2">Gagal memuat data</td></tr>';
          loading.style.display = "none";
        });
    }

    function getStatusBadge(status) {
      switch (status) {
        case 'sold':
          return '<span class="badge badge-dark">Sold</span>';
        case 'booking':
          return '<span class="badge badge-warning">Booking</span>';
        case 'kosong':
          return '<span class="badge badge-success">Kosong</span>';
        default:
          return '<span class="badge badge-secondary">Unknown</span>';
      }
    }
  </script>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section">
      <div class="hero-bg">
        <img src="assets/img/hero-bg-light.webp" alt="">
      </div>
      <div class="container text-center">
        <div class="d-flex flex-column justify-content-center align-items-center">
          <h1 data-aos="fade-up">Welcome to <span>NeperSport</span></h1>
          <p data-aos="fade-up" data-aos-delay="100">Quickly start your project now and set the stage for success<br>
          </p>
          <div class="d-flex" data-aos="fade-up" data-aos-delay="200">
            <a href="#about" class="btn-get-started">Get Started</a>
            <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8"
              class="glightbox btn-watch-video d-flex align-items-center"><i class="bi bi-play-circle"></i><span>Watch
                Video</span></a>
          </div>
          <img src="assets/img/hero2.png" class="img-fluid hero-img" alt="" data-aos="zoom-out" data-aos-delay="300">
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- Featured Services Section -->
    <section id="featured-services" class="featured-services section light-background">

      <div class="container">

        <div class="row gy-4">

          <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><i class="bi bi-briefcase"></i></div>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Booking Mudah</a></h4>
                <p class="description">Pesan lapangan futsal dengan mudah melalui website dan nikmati kenyamanan proses
                  booking.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><i class="bi bi-card-checklist"></i></div>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Jadwal Terupdate</a></h4>
                <p class="description">Lihat jadwal lapangan yang tersedia dan lakukan booking sesuai waktu yang Anda
                  inginkan.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item d-flex">
              <div class="icon flex-shrink-0"><i class="bi bi-bar-chart"></i></div>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Sistem Pembayaran Aman</a></h4>
                <p class="description">Melakukan pembayaran DP dengan aman untuk memastikan lapangan telah dibooking
                  sebelum digunakan.</p>
              </div>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Featured Services Section -->


    <!-- About Section -->
    <section id="about" class="about section">

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
            <p class="who-we-are">Who We Are</p>
            <h3>Lapangan futsal SMKN 1 CIREBON</h3>
            <p class="fst-italic">
              Kami hadir sebagai solusi modern untuk memenuhi kebutuhan Anda dalam melakukan booking lapangan futsal.
              Dengan sistem yang cepat, transparan, dan terpercaya, kami memastikan pengalaman bermain futsal Anda
              semakin mudah dan menyenangkan.
            </p>
            <p>Di sini, Anda dapat:</p>
            <ul>
              <li><i class="bi bi-check-circle"></i> <span>Melihat jadwal lapangan secara real-time.</span></li>
              <li><i class="bi bi-check-circle"></i> <span>Melakukan booking dengan cepat tanpa kerumitan.</span></li>
              <li><i class="bi bi-check-circle"></i> <span>Memastikan waktu bermain Anda aman tanpa risiko bentrok
                  jadwal.</span></li>
              <li><i class="bi bi-check-circle"></i> <span>Harga yang terjangkau .</span></li>
            </ul>
            <a href="#" class="read-more"><span>Read More</span><i class="bi bi-arrow-right"></i></a>
          </div>

          <div class="col-lg-6 about-images" data-aos="fade-up" data-aos-delay="200">
            <div class="row gy-4">
              <div class="col-lg-6">
                <img src="assets/img/lapangan3.jpg" class="img-fluid" alt="">
              </div>
              <div class="col-lg-6">
                <div class="row gy-4">
                  <div class="col-lg-12">
                    <img src="assets/img/lapangan.jpg" class="img-fluid" alt="">
                  </div>
                  <div class="col-lg-12">
                    <img src="assets/img/lapangan2.jpg" class="img-fluid" alt="">
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>

      </div>
    </section><!-- /About Section -->

    <!-- Clients Section -->
    <section id="clients" class="clients section">

      <div class="container" data-aos="fade-up">

        <div class="row gy-4">

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="assets/img/clients/tekun.png" class="img-fluid" alt="">
          </div><!-- End Client Item -->

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="assets/img/clients/garudasport.png" class="img-fluid" alt="">
          </div><!-- End Client Item -->

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="assets/img/clients/rank.jpg" class="img-fluid" alt="">
          </div><!-- End Client Item -->

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="assets/img/clients/tuparev.jpg" class="img-fluid" alt="">
          </div><!-- End Client Item -->

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="assets/img/clients/f.jpg" class="img-fluid" alt="">
          </div><!-- End Client Item -->

          <div class="col-xl-2 col-md-3 col-6 client-logo">
            <img src="assets/img/clients/client-6.png" class="img-fluid" alt="">
          </div><!-- End Client Item -->

        </div>

      </div>

    </section><!-- /Clients Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Features</h2>
        <p>Kemudahan Maksimal untuk Pengalaman Terbaik Anda</p>
      </div><!-- End Section Title -->

      <div class="container">
        <div class="row justify-content-between">

          <div class="col-lg-5 d-flex align-items-center">

            <ul class="nav nav-tabs" data-aos="fade-up" data-aos-delay="100">
              <li class="nav-item">
                <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
                  <i class="bi bi-calendar"></i>
                  <div>
                    <h4 class="d-none d-lg-block">Real-Time Booking</h4>
                    <p>
                      Pesan jadwal lapangan dengan cepat dan mudah. Jadwal selalu diperbarui secara real-time untuk
                      memastikan waktu bermain Anda aman tanpa bentrok.
                    </p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
                  <i class="bi bi-credit-card"></i>
                  <div>
                    <h4 class="d-none d-lg-block">Kemudahan Transaksi</h4>
                    <p>
                      Booking dan pembayaran praktis hanya dengan beberapa langkah. Nikmati pengalaman transaksi yang
                      cepat dan aman.
                    </p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-3">
                  <i class="bi bi-chat"></i>
                  <div>
                    <h4 class="d-none d-lg-block">Dukungan Pelanggan</h4>
                    <p>
                      Kami berkomitmen untuk memberikan pelayanan terbaik. Tim kami selalu siap membantu Anda jika ada
                      pertanyaan atau kendala, sehingga pengalaman Anda tetap lancar dan menyenangkan.
                    </p>
                  </div>
                </a>
              </li>
            </ul><!-- End Tab Nav -->

          </div>

          <div class="col-lg-6">

            <div class="tab-content" data-aos="fade-up" data-aos-delay="200">

              <div class="tab-pane fade active show" id="features-tab-1">
                <img src="assets/img/tabs-1.jpg" alt="" class="img-fluid">
              </div><!-- End Tab Content Item -->

              <div class="tab-pane fade" id="features-tab-2">
                <img src="assets/img/tabs-2.jpg" alt="" class="img-fluid">
              </div><!-- End Tab Content Item -->

              <div class="tab-pane fade" id="features-tab-3">
                <img src="assets/img/tabs-3.jpg" alt="" class="img-fluid">
              </div><!-- End Tab Content Item -->
            </div>

          </div>

        </div>

      </div>

    </section><!-- /Features Section -->

    <!-- Features Details Section -->
    <section id="features-details" class="features-details section">

      <div class="container">
        <!-- Modal -->
        <div class="modal fade" id="modal-booking" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalLabel">Form Booking Lapangan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form action="proses_booking.php" method="POST" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="nama_user">Nama</label>
                    <input type="text" id="nama_user" name="nama_user" class="form-control" value="<?php echo htmlspecialchars($nama_user); ?>" readonly required>
                  </div>

                  <div class="form-group">
                    <label for="date">Tanggal</label>
                    <input type="date" id="date" name="jadwal_booking" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label>Hari</label>
                    <input type="text" id="day" class="form-control" disabled>
                  </div>

                  <div class="form-group">
                    <label for="time-slot">Jam</label>
                    <div id="jam" class="d-flex flex-wrap"></div>
                  </div>

                  <div class="form-group">
                    <label for="price">Harga</label>
                    <input type="text" id="price" class="form-control" disabled>
                  </div>

                  <!-- Pilihan Pembayaran -->
                  <div class="form-group">
                    <label for="via_pembayaran">Pembayaran Via</label>
                    <select id="via_pembayaran" name="via_pembayaran" class="form-control" required>
                      <option value="">-- Pilih Pembayaran --</option>
                      <option value="Dana">Dana</option>
                      <option value="BCA">BCA</option>
                    </select>
                  </div>

                  <!-- Nomor Rekening (otomatis muncul) -->
                  <div class="form-group" id="no_rekening_container" style="display: none;">
                    <label for="no_rek">Nomor Rekening</label>
                    <input type="text" id="no_rek_display" class="form-control" readonly style="background-color: #e9ecef; color: #495057; cursor: not-allowed;">
                    <input type="hidden" id="no_rek" name="nomor_pembayaran">
                  </div>

                  <!-- Bukti Transfer -->
                  <div class="form-group">
                    <label for="bukti_transfer"><b>Bukti Transfer:</b></label><br>
                    <input type="file" name="bukti_transfer" required><br>
                    <small style="color: red;"><b>Note: Minimal DP 20.000</b></small>
                  </div>

                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Booking</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <script>
          document.addEventListener("DOMContentLoaded", function() {
            let isMember = <?php echo json_encode($is_member); ?>;
            let jamTerbooking = <?= json_encode($jam_terbooking); ?>;
            let jamContainer = document.getElementById("jam");
            let hargaInput = document.getElementById("price");
            let dayInput = document.getElementById("day");

            document.getElementById("date").addEventListener("change", function() {
              let tanggal = new Date(this.value);
              let hari = tanggal.getDay();

              let dayNames = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
              dayInput.value = dayNames[hari];

              jamContainer.innerHTML = "";
              hargaInput.value = "";

              let jamList = getJamList(hari);
              jamList.forEach(({
                jam,
                harga
              }) => {
                let isBooked = jamTerbooking.some(bookedJam => normalizeTime(bookedJam) === normalizeTime(jam));
                let adjustedJam = applyBonusTime(jam);

                let div = document.createElement("div");
                div.classList.add("p-2");

                div.innerHTML = `
          <label style="${isBooked ? 'color: red; text-decoration: line-through; font-weight: bold;' : ''}">
              <input type="radio" name="jam" value="${adjustedJam}" data-harga="${harga}" ${isBooked ? 'disabled' : ''}>
              ${adjustedJam} (${harga.toLocaleString("id-ID").replace(/,/g, ".")})
          </label>
      `;
                jamContainer.appendChild(div);
              });

              document.querySelectorAll('input[name="jam"]').forEach((slot) => {
                slot.addEventListener("change", function() {
                  hargaInput.value = parseInt(this.getAttribute("data-harga")).toLocaleString("id-ID").replace(/,/g, ".");
                });
              });
            });

            function getJamList(hari) {
              if (hari >= 1 && hari <= 5) {
                return [{
                    jam: "17:00-18:00",
                    harga: 120000
                  },
                  {
                    jam: "18:00-19:00",
                    harga: 120000
                  },
                  {
                    jam: "19:00-20:00",
                    harga: 120000
                  },
                  {
                    jam: "20:00-21:00",
                    harga: 120000
                  },
                  {
                    jam: "21:00-22:00",
                    harga: 120000
                  }
                ];
              } else {
                return [{
                    jam: "08:00-09:00",
                    harga: 80000
                  },
                  {
                    jam: "09:00-10:00",
                    harga: 80000
                  },
                  {
                    jam: "10:00-11:00",
                    harga: 80000
                  },
                  {
                    jam: "11:00-12:00",
                    harga: 80000
                  },
                  {
                    jam: "13:00-14:00",
                    harga: 100000
                  },
                  {
                    jam: "14:00-15:00",
                    harga: 100000
                  },
                  {
                    jam: "15:00-16:00",
                    harga: 100000
                  },
                  {
                    jam: "16:00-17:00",
                    harga: 130000
                  },
                  {
                    jam: "17:00-18:00",
                    harga: 130000
                  },
                  {
                    jam: "18:00-19:00",
                    harga: 130000
                  },
                  {
                    jam: "19:00-20:00",
                    harga: 130000
                  },
                  {
                    jam: "20:00-21:00",
                    harga: 130000
                  },
                  {
                    jam: "21:00-22:00",
                    harga: 130000
                  },
                  {
                    jam: "22:00-23:00",
                    harga: 130000
                  }
                ];
              }
            }

            function applyBonusTime(jam) {
              if (!isMember) return jam;
              let [start, end] = jam.split("-").map(time => time.trim());
              let [endHour, endMinute] = end.split(":").map(Number);
              endHour += 1;
              if (endHour === 24) endHour = "00";
              let newEnd = `${String(endHour).padStart(2, "0")}:${String(endMinute).padStart(2, "0")}`;
              return `${start}-${newEnd} (Bonus 1 jam)`;
            }

            function normalizeTime(jam) {
              return jam.trim().replace(/\s+/g, "");
            }

            // ✅ Script No Rek Otomatis:
            const noRekContainer = document.getElementById("no_rekening_container");
            const viaPembayaran = document.getElementById("via_pembayaran");
            const noRekDisplay = document.getElementById("no_rek_display");
            const noRekHidden = document.getElementById("no_rek");

            const nomorRek = {
              "Dana": "08123456789",
              "BCA": "1234567890"
            };

            viaPembayaran.addEventListener("change", function() {
              const metode = this.value;
              if (nomorRek[metode]) {
                noRekContainer.style.display = "block";
                noRekDisplay.value = nomorRek[metode];
                noRekHidden.value = nomorRek[metode];
              } else {
                noRekContainer.style.display = "none";
                noRekDisplay.value = "";
                noRekHidden.value = "";
              }
            });
          });
        </script>

      </div>

      <!-- Modal Jadwal -->
      <div class="modal fade" id="dp-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="modalLabel">Pembayaran DP</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="proses_booking.php" method="POST" enctype="multipart/form-data">
                <label>Nama User:</label>
                <input type="text" name="nama_user" required><br>

                <label>Jadwal Booking:</label>
                <input type="datetime-local" name="jadwal_booking" required><br>

                <label>Via Pembayaran:</label>
                <select name="via_pembayaran" required>
                  <option value="Transfer Bank">Transfer Bank</option>
                  <option value="E-Wallet">E-Wallet</option>
                </select><br>

                <label>Nomor Pembayaran:</label>
                <input type="text" name="nomor_pembayaran"><br>

                <label>Upload Bukti Transfer:</label>
                <input type="file" name="bukti_transfer" required><br>

                <button type="submit">Kirim Booking</button>
              </form>

            </div>
          </div>
        </div>
      </div>


      </div>

    </section><!-- /Features Details Section -->



    </div>

    </div>

    </section><!-- /Services Section -->

    <!-- More Features Section -->
    <section id="more-features" class="more-features section">

      <div class="container">

        <div class="row justify-content-around gy-4">

          <div class="col-lg-6 d-flex flex-column justify-content-center order-2 order-lg-1" data-aos="fade-up"
            data-aos-delay="100">
            <h3>Lapangan Futsal Berkualitas Tinggi</h3>
            <p>Rasakan pengalaman bermain di lapangan dengan permukaan yang dirawat secara profesional, cocok untuk
              pertandingan ataupun latihan.</p>

            <div class="row">

              <div class="col-lg-6 icon-box d-flex">
                <i class="bi bi-star flex-shrink-0"></i>
                <div>
                  <h4>Lapangan Berkualitas</h4>
                  <p>Lapangan terawat dengan standar tinggi untuk pengalaman bermain terbaik.</p>
                </div>
              </div><!-- End Icon Box -->

              <div class="col-lg-6 icon-box d-flex">
                <i class="bi bi-tools flex-shrink-0"></i>
                <div>
                  <h4>Fasilitas Pendukung Lengkap</h4>
                  <p>Termasuk ruang ganti nyaman, parkir luas, dan kipas angin besar.</p>
                </div>
              </div><!-- End Icon Box -->

              <div class="col-lg-6 icon-box d-flex">
                <i class="bi bi-laptop flex-shrink-0"></i>
                <div>
                  <h4>Sistem Booking Online</h4>
                  <p>Pesan jadwal kapan saja dengan mudah melalui sistem online kami.</p>
                </div>
              </div><!-- End Icon Box -->

              <div class="col-lg-6 icon-box d-flex">
                <i class="bi bi-tags flex-shrink-0"></i>
                <div>
                  <h4>Harga Transparan</h4>
                  <p>Informasi harga yang jelas tanpa biaya tambahan tersembunyi.</p>
                </div>
              </div><!-- End Icon Box -->

            </div>

          </div>

          <div class="features-image col-lg-5 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="200">
            <img src="assets/img/features-3.jpg" alt="">
          </div>

        </div>

      </div>

    </section><!-- /More Features Section -->

    <!-- Pricing Section -->
    <section id="pricing" class="pricing section">
      <div class="container">
        <div class="row">
          <!-- Kolom untuk daftar lapangan -->
          <div class="col-lg-8">
            <div class="section-title" data-aos="fade-up">
              <h2>Daftar Lapangan</h2>
            </div>

            <div class="container d-flex justify-content-center">
              <div class="col-lg-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="pricing-item featured card text-center">
                  <div class="card">
                    <!-- Carousel Start -->
                    <div id="lapanganCarousel" class="carousel slide" data-bs-ride="carousel">
                      <div class="carousel-inner" style="border-radius: 10px;">
                        <div class="carousel-item active">
                          <img src="assets/img/lapangan.jpg" class="d-block w-100" alt="Lapangan 1"
                            style="height: 200px; object-fit: cover; border-radius: 10px;">
                        </div>
                        <div class="carousel-item">
                          <img src="assets/img/lapangan2.jpg" class="d-block w-100" alt="Lapangan 2"
                            style="height: 200px; object-fit: cover; border-radius: 10px;">
                        </div>
                        <div class="carousel-item">
                          <img src="assets/img/lapangan3.jpg" class="d-block w-100" alt="Lapangan 3"
                            style="height: 200px; object-fit: cover; border-radius: 10px;">
                        </div>
                      </div>
                      <!-- Carousel Controls -->
                      <button class="carousel-control-prev" type="button" data-bs-target="#lapanganCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                      </button>
                      <button class="carousel-control-next" type="button" data-bs-target="#lapanganCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                      </button>
                    </div>
                    <!-- Carousel End -->

                    <div class="card-body">
                      <h5 class="card-title">Lapangan Futsal</h5>
                      <p class="card-text">Neperking</p>
                      <p class="text-muted"><i class="fas fa-futbol"></i> Futsal </p>
                      <p class="text-primary fw-bold">Mulai <span class="text-dark">Rp50,000</span> /sesi</p>
                      <a href="#" class="btn btn-warning text-white fw-bold" data-toggle="modal"
                        data-target="#modal-booking">Booking Sekarang</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Kolom untuk Peringatan -->
          <div class="col-lg-4 d-flex align-items-center">
            <div class="alert alert-warning text-dark w-100" role="alert"
              style="border-radius: 10px; padding: 15px; margin-top: 50px;">
              <h4 class="alert-heading">⚠️ Perhatian!</h4>
              <p><strong>Mohon maaf atas ketidaknyamanan.</strong></p>
              <ul class="mb-0">
                <li>🕘 Senin: Kegiatan Zumba - 09:00</li>
                <li>⚽ Minggu: Eskul Futsal - 12:00</li>
              </ul>
            </div>
          </div>


        </div>
      </div>
    </section>



    <!--  -->

    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <p>Nomer telpon, Alamat, Email tertara di bawah</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up"
              data-aos-delay="200">
              <i class="bi bi-geo-alt"></i>
              <h3>Address</h3>
              <p>Jl. Perjuangan By Pass Sunyaragi, Cirebon, Indonesia 45132.</p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-lg-3 col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up"
              data-aos-delay="300">
              <i class="bi bi-telephone"></i>
              <h3>Call Us</h3>
              <p>+62-0231-480202</p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-lg-3 col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up"
              data-aos-delay="400">
              <i class="bi bi-envelope"></i>
              <h3>Email Us</h3>
              <p>info@smkn1-cirebon.sch.id</p>
            </div>
          </div><!-- End Info Item -->

        </div>

        <div class="row gy-4 mt-1">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3962.2830232659203!2d108.53415787441728!3d-6.7352859658546205!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f1df0e55b2ed3%3A0x51cf481547b4b319!2sSMK%20Negeri%201%20Cirebon!5e0!3m2!1sid!2sid!4v1737877922343!5m2!1sid!2sid"
              frameborder="0" style="border:0; width: 100%; height: 400px;" allowfullscreen="" loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div><!-- End Google Maps -->

          <div class="col-lg-6">
            <form action="forms/contact.php" method="post" class="php-email-form" data-aos="fade-up"
              data-aos-delay="400">
              <div class="row gy-4">

                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="Your Name" required="">
                </div>

                <div class="col-md-6 ">
                  <input type="email" class="form-control" name="email" placeholder="Your Email" required="">
                </div>

                <div class="col-md-12">
                  <input type="text" class="form-control" name="subject" placeholder="Subject" required="">
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="message" rows="6" placeholder="Message" required=""></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading">Loading</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Your message has been sent. Thank you!</div>

                  <button type="submit">Send Message</button>
                </div>

              </div>
            </form>
          </div><!-- End Contact Form -->

        </div>

      </div>

    </section><!-- /Contact Section -->

  </main>

  <footer id="footer" class="footer position-relative light-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename">NeperSport</span>
          </a>
          <div class="footer-contact pt-3">
            <p>SMK Negeri 1 Kota Cirebon</p>
            <p>Jl. Perjuangan, Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+62-0231-480202</span></p>
            <p><strong>Email:</strong> <span>info@smkn1-cirebon.sch.id</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Terms of service</a></li>
            <li><a href="#">Privacy policy</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="#">Booking Lapangan Futsal</a></li>
            <li><a href="#">Jadwal Transparan</a></li>
            <li><a href="#">Pembayaran Mudah</a></li>
            <li><a href="#">Fasilitas Terlengkap</a></li>
            <li><a href="#">Layanan Pelanggan</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-12 footer-newsletter">
          <h4>Our Newsletter</h4>
          <p>Daftar sekarang dan dapatkan informasi terbaru tentang promosi dan jadwal lapangan spesial!</p>
          <form action="forms/newsletter.php" method="post" class="php-email-form">
            <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscribe"></div>
            <div class="loading">Loading</div>
            <div class="error-message"></div>
            <div class="sent-message">Your subscription request has been sent. Thank you!</div>
          </form>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">SMKN 1 CIREBON</strong></p>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
  <!-- Floating Button WhatsApp -->
  <a href="https://wa.me/6287877691446?text=Halo,%20admin%20!" target="_blank" class="wa-floating">
    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="Chat via WhatsApp">
  </a>

  <style>
    .wa-floating {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #25D366;
      padding: 10px;
      border-radius: 50%;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
      transition: transform 0.2s ease-in-out;
    }

    .wa-floating img {
      width: 50px;
      height: 50px;
    }

    .wa-floating:hover {
      transform: scale(1.1);
      background-color: #1ebe57;
    }
  </style>

</body>

</html>
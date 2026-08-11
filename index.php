<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halo SarPras</title>
  <style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
}

body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: #ffffff;
}

/* Header / Navbar */
.navbar {
  background-color: #2e8b22;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 40px;
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 15px;
}

.logo-img {
  height: 90px;
  width: auto;
}

.brand-title {
  color: #ffffff;
  font-size: 2.2rem;
  font-weight: bold;
}

.auth-buttons {
  display: flex;
  gap: 20px;
}

.btn {
  text-decoration: none;
  background-color: #d1bc4d;
  color: #222222;
  padding: 12px 40px;
  border-radius: 25px;
  font-size: 1.1rem;
  font-weight: 500;
  transition: background-color 0.2s ease;
}

.btn:hover {
  background-color: #bfa83b;
}

/* Content Area */
.content-container {
  display: flex;
  flex: 1;
  width: 100%;
}

.banner-section {
  width: 50%;
  border-right: 1px solid #000;
}

.banner-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Styling Bagian Tentang Kami */
.about-section {
  width: 50%;
  padding: 40px 45px;
  background-color: #ffffff;
}

.about-section h1 {
  font-size: 2.2rem;
  font-weight: 800;
  color: #1f5c1f; /* Hijau SarPras */
  margin-bottom: 20px;
  position: relative;
  display: inline-block;
  letter-spacing: -0.5px;
}

/* Garis dekorasi di bawah Judul */
.about-section h1::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -6px;
  width: 60px;
  height: 4px;
  background-color: #fbd65c; /* Kuning SarPras */
  border-radius: 2px;
}

.about-section p {
  font-size: 1.05rem;
  line-height: 1.7;
  color: #444444;
  margin-bottom: 16px;
  text-align: justify;
}

.about-section strong {
  color: #1f5c1f;
}

/* Highlight Box untuk Jargon / Slogan */
.slogan-box {
  margin-top: 25px;
  padding: 15px 20px;
  background-color: #f4fbf4;
  border-left: 4px solid #2b802b;
  border-radius: 0 10px 10px 0;
  font-weight: 600;
  color: #2b802b;
  font-style: italic;
  font-size: 0.98rem;
}

/* Footer */
.footer {
  background-color: #79c753;
  text-align: center;
  padding: 18px 20px;
  border-top: 1px solid #000;
}

.footer p {
  color: #000000;
  font-size: 1.1rem;
}
  </style>
</head>
<body>

  <!-- Navbar / Header -->
  <header class="navbar">
    <div class="logo-container">
      <img src="img/logo sapras.png" alt="Logo Halo SarPras" class="logo-img">
      <span class="brand-title">Halo SarPras</span>
    </div>
    <div class="auth-buttons">
        <a href="auth/login.php" class="btn btn-login">Login</a>
        <a href="auth/register.php" class="btn btn-register">Register</a>
    </div>
  </header>

  <!-- Content Section -->
  <main class="content-container">
    <div class="banner-section">
      <img src="img/Banner.jpeg" alt="Banner Halo SarPras" class="banner-img">
    </div>
    <div class="about-section">
    <h1>Tentang Kami</h1>
    
    <p>
        <strong>Halo SarPras SMK Taruna Bangsa</strong> adalah platform layanan pengaduan sarana, prasarana, dan kebersihan di <strong>SMK Taruna Bangsa</strong>. Diciptakan untuk mempermudah seluruh warga sekolah dalam melaporkan kerusakan fasilitas secara cepat, transparan, dan terintegrasi.
    </p>

    <p>
        Melalui sistem ini, setiap laporan yang masuk akan langsung diverifikasi oleh tim admin dan ditindaklanjuti oleh petugas secara <em>real-time</em>.
    </p>

    <div class="slogan-box">
        "Laporkan, Kami Tindaklanjuti, Sekolah Lebih Nyaman!"
    </div>
</div>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <p>2026 Halo SarPras - All Rights Reserved. Designed by Group 4 - Disclaimer</p>
  </footer>

</body>
</html>
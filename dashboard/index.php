<?php
require 'auth_check.php';

$envFile = '../.env';
if (file_exists($envFile)) {
  $env = file($envFile, FILE_IGNORE_NEW_LINES);
} else {
  $env = [];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Configuration</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Fira+Code:wght@400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    /* Styling untuk Notifikasi Popup (Toast) */
    .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .toast {
      min-width: 300px;
      padding: 15px 20px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 12px;
      color: white;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      display: flex;
      align-items: center;
      gap: 15px;
      transform: translateX(120%);
      /* Sembunyi di kanan */
      transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      font-size: 0.9rem;
    }

    .toast.show {
      transform: translateX(0);
      /* Muncul */
    }

    .toast.success {
      border-left: 5px solid #2ecc71;
    }

    .toast.error {
      border-left: 5px solid #e74c3c;
    }

    /* Animasi Loading pada tombol */
    .fa-spin-fast {
      animation: fa-spin 1s infinite linear;
    }

    /* --- Style Dasar (Sama seperti sebelumnya) --- */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      font-family: 'Poppins', sans-serif;
      padding: 20px;
    }

    @keyframes gradientBG {
      0% {
        background-position: 0% 50%;
      }

      50% {
        background-position: 100% 50%;
      }

      100% {
        background-position: 0% 50%;
      }
    }

    .dashboard-container {
      width: 100%;
      max-width: 800px;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
      color: #fff;
      display: flex;
      flex-direction: column;
      max-height: 95vh;
    }

    .header {
      padding: 20px 25px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header h2 {
      font-size: 1.4rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logout-btn {
      text-decoration: none;
      color: #fff;
      background: rgba(255, 75, 75, 0.7);
      padding: 8px 15px;
      border-radius: 8px;
      font-size: 0.85rem;
      transition: all 0.3s;
    }

    .logout-btn:hover {
      background: rgba(255, 75, 75, 1);
    }

    /* Search & Alert Area */
    .top-controls {
      padding: 15px 25px 0;
    }

    .search-box input {
      width: 100%;
      padding: 10px 15px;
      border-radius: 10px;
      border: none;
      background: rgba(255, 255, 255, 0.8);
      font-family: 'Poppins', sans-serif;
      outline: none;
    }

    /* Scroll Area untuk .env */
    .scroll-area {
      overflow-y: auto;
      padding: 20px 25px;
      flex-grow: 1;
      /* Mengisi ruang kosong */
    }

    /* Scrollbar Styling */
    .scroll-area::-webkit-scrollbar {
      width: 6px;
    }

    .scroll-area::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 3px;
    }

    .config-item {
      background: rgba(255, 255, 255, 0.1);
      padding: 12px;
      border-radius: 10px;
      margin-bottom: 10px;
      border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .config-label {
      display: block;
      font-size: 0.8rem;
      color: #ddd;
      margin-bottom: 5px;
      font-weight: 600;
    }

    .config-input {
      width: 100%;
      padding: 8px 12px;
      background: rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 6px;
      color: #a5f3fc;
      font-family: 'Fira Code', monospace;
      font-size: 0.9rem;
      outline: none;
    }

    .config-input:focus {
      background: rgba(0, 0, 0, 0.4);
      border-color: #23a6d5;
    }

    /* --- Bagian Bawah: Actions & Save --- */
    .footer-section {
      padding: 20px 25px;
      border-top: 1px solid rgba(255, 255, 255, 0.2);
      background: rgba(0, 0, 0, 0.1);
      border-radius: 0 0 20px 20px;
    }

    .tools-grid {
      display: grid;
      grid-template-columns: 1fr auto;
      /* Kiri: Manual Tools, Kanan: Save Config */
      gap: 20px;
      align-items: center;
    }

    .manual-tools h3 {
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 10px;
      color: #eee;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Tombol Save (Biru) */
    .btn-save {
      background: #fff;
      color: #333;
      border: none;
      padding: 12px 30px;
      border-radius: 30px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      transition: 0.3s;
    }

    .btn-save:hover {
      background: #23a6d5;
      color: white;
      transform: translateY(-2px);
    }

    /* Tombol Run Report (Ungu/Pink) */
    .btn-run {
      background: linear-gradient(45deg, #ff6b6b, #f06595);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      font-size: 0.9rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 10px rgba(240, 101, 149, 0.3);
      transition: 0.3s;
    }

    .btn-run:hover {
      filter: brightness(1.1);
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(240, 101, 149, 0.5);
    }

    /* Alert Messages */
    .alert {
      padding: 10px 15px;
      margin-bottom: 15px;
      border-radius: 8px;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .alert-success {
      background: rgba(46, 204, 113, 0.2);
      border: 1px solid #2ecc71;
      color: #2ecc71;
    }

    .alert-error {
      background: rgba(231, 76, 60, 0.2);
      border: 1px solid #e74c3c;
      color: #e74c3c;
    }
  </style>
</head>

<body>

  <div class="dashboard-container">

    <div class="header">
      <h2><i class="fas fa-server"></i> Control Panel</h2>
      <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="top-controls">
      <?php if (isset($_GET['run'])): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i> Laporan harian berhasil dijalankan!
        </div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
          <i class="fas fa-times-circle"></i> Gagal menjalankan laporan.
        </div>
      <?php endif; ?>

      <div class="search-box">
        <input type="text" id="searchInput" placeholder="🔍 Cari key konfigurasi...">
      </div>
    </div>

    <form method="POST" action="save.php" id="configForm" style="display: flex; flex-direction: column; flex-grow: 1; overflow: hidden;">
      <div class="scroll-area">
        <?php if (!empty($env)): ?>
          <?php foreach ($env as $line): ?>
            <?php
            if (trim($line) === '' || str_starts_with(trim($line), '#')) continue;
            $parts = explode('=', $line, 2);
            if (count($parts) < 2) continue;
            [$key, $value] = $parts;
            ?>
            <div class="config-item">
              <label class="config-label"><?= htmlspecialchars(trim($key)) ?></label>
              <input type="text" class="config-input" name="env[<?= htmlspecialchars(trim($key)) ?>]" value="<?= htmlspecialchars(trim($value)) ?>" spellcheck="false">
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </form>

    <div class="footer-section">
      <div class="tools-grid">

        <div class="manual-tools">
          <h3><i class="fas fa-robot"></i> Operasi Manual</h3>

          <div class="toast-container" id="toastContainer"></div>
          <button type="button" id="btnRunReport" class="btn-run">
            <i class="fas fa-rocket" id="btnIcon"></i>
            <span id="btnText">Jalankan Laporan</span>
          </button>
        </div>

        <div style="text-align: right;">
          <button type="submit" form="configForm" class="btn-save">
            <i class="fas fa-save"></i> SIMPAN KONFIGURASI
          </button>
        </div>

      </div>
    </div>

  </div>

  <script>
    document.getElementById('btnRunReport').addEventListener('click', function() {

      // 1. Konfirmasi User
      if (!confirm('Jalankan laporan harian sekarang?')) return;

      // 2. Siapkan elemen UI
      const btn = this;
      const icon = document.getElementById('btnIcon');
      const text = document.getElementById('btnText');

      // 3. Ubah tombol jadi mode "Loading"
      const originalText = text.innerText;
      const originalIcon = icon.className;

      btn.disabled = true;
      text.innerText = "Memproses...";
      icon.className = "fas fa-circle-notch fa-spin-fast"; // Ikon loading putar
      btn.style.opacity = "0.7";

      // 4. Kirim Request ke Server (AJAX)
      fetch('run_report.php')
        .then(response => response.json()) // Ubah respon jadi JSON
        .then(data => {
          // 5. Tampilkan Notifikasi berdasarkan hasil
          showToast(data.message, data.status);
        })
        .catch(error => {
          // Jika koneksi error
          showToast("Terjadi kesalahan koneksi server.", "error");
          console.error('Error:', error);
        })
        .finally(() => {
          // 6. Kembalikan tombol ke semula
          btn.disabled = false;
          text.innerText = originalText;
          icon.className = originalIcon;
          btn.style.opacity = "1";
        });
    });

    // Fungsi membuat notifikasi Toast Keren
    function showToast(message, type) {
      const container = document.getElementById('toastContainer');

      // Buat elemen div baru
      const toast = document.createElement('div');
      toast.className = `toast ${type}`;

      // Tentukan ikon
      const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
      const color = type === 'success' ? '#2ecc71' : '#e74c3c';

      toast.innerHTML = `
      <i class="fas ${iconClass}" style="color: ${color}; font-size: 1.2rem;"></i>
      <span>${message}</span>
    `;

      container.appendChild(toast);

      // Trigger animasi muncul (sedikit delay agar transisi CSS jalan)
      setTimeout(() => {
        toast.classList.add('show');
      }, 100);

      // Hapus notifikasi setelah 4 detik
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
          toast.remove();
        }, 500); // Tunggu animasi slide-out selesai baru hapus dari DOM
      }, 4000);
    }
  </script>

</body>

</html>
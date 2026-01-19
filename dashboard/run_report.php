<?php
require 'auth_check.php';

// Set header JSON di awal
header('Content-Type: application/json');

// Matikan error display agar tidak merusak JSON
error_reporting(0);
ini_set('display_errors', 0);

// Mulai buffer output
ob_start();

try {
  // Include file laporan yang sudah direvisi (menggunakan return, bukan die)
  // Gunakan include, bukan require_once, agar variabel scope aman
  include '../laporan_harian.php';

  // Ambil output dari echo yang ada di dalam laporan_harian.php
  $output = ob_get_clean();
  $output = trim($output);

  // Tentukan status berdasarkan output
  if (empty($output)) {
    // Jika tidak ada output sama sekali
    echo json_encode(['status' => 'success', 'message' => 'Proses selesai (Silent).']);
  } elseif (str_contains(strtolower($output), 'gagal') || str_contains(strtolower($output), 'error') || str_contains(strtolower($output), 'tidak ditemukan')) {
    // Jika teks mengandung kata error/gagal
    echo json_encode(['status' => 'error', 'message' => $output]);
  } else {
    // Jika output normal (misal: "Laporan berhasil dikirim" atau "Tidak ada data...")
    echo json_encode(['status' => 'success', 'message' => $output]);
  }
} catch (Throwable $e) {
  if (ob_get_length()) ob_end_clean();
  echo json_encode(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()]);
}
exit;

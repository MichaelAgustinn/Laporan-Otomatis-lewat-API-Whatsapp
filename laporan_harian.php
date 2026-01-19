<?php

/*
Lisensi Penggunaan
Hak Cipta (c) 2026 Michael Agustin
*/

// Fungsi Helper untuk Output & Stop (Pengganti die)
if (!function_exists('stop_process')) {
  function stop_process($message)
  {
    echo $message;
    return; // Gunakan return, jangan exit/die
  }
}

function parseGvizDate($gvizDate)
{
  if (!is_string($gvizDate)) return null;
  if (preg_match('/Date\((\d+),(\d+),(\d+)\)/', $gvizDate, $m)) {
    $year  = (int) $m[1];
    $month = (int) $m[2] + 1;
    $day   = (int) $m[3];
    return new DateTime("$year-$month-$day");
  }
  return null;
}

function loadEnv($path = null)
{
  if ($path === null) $path = __DIR__ . '/.env';

  if (!file_exists($path)) {
    echo '.env file tidak ditemukan: ' . $path;
    return false; // Return false jika gagal
  }

  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($name, $value) = explode('=', $line, 2);
    putenv(trim($name) . '=' . trim($value));
  }
  return true;
}

// Load Env
if (!loadEnv()) return; // Stop jika env gagal load

// Deklarasi variable
$sheetId   = getenv('GOOGLE_SHEET_ID');
$token     = getenv('FONNTE_TOKEN');
$target    = getenv('WHATSAPP_TARGET');
$dashboard = getenv('LOOKER_DASHBOARD_URL');

// Stop jika env tidak lengkap
if (!$sheetId || !$token || !$target) {
  echo 'Konfigurasi .env belum lengkap';
  return;
}

$url = "https://docs.google.com/spreadsheets/d/$sheetId/gviz/tq?tqx=out:json";

// Gunakan @ untuk menyembunyikan warning jika koneksi gagal
$response = @file_get_contents($url);
if ($response === false) {
  echo 'Gagal mengambil data Google Sheet (Cek Koneksi/ID)';
  return;
}

$json = json_decode(substr($response, 47, -2), true);

if (!isset($json['table']['rows'])) {
  echo 'Format data Google Sheet tidak valid';
  return;
}

$rows = $json['table']['rows'];

$pesan = "📄 LAPORAN DATA SERTIFIKAT\n\n";
$no = 1;
$today = new DateTime();
$limitDate = (clone $today)->modify('+3 months');

foreach ($rows as $index => $row) {
  $nomor = $row['c'][3]['v'] ?? '-';
  $namaProduk = $row['c'][4]['v'] ?? '-';
  $expiredRaw = $row['c'][7]['v'] ?? null;
  $notifikasi = $row['c'][count($row['c']) - 1]['v'] ?? false;

  // Pengecekan checkbox
  if ($notifikasi !== true && $notifikasi !== 'TRUE') continue;

  // Parse tanggal
  $expiredDate = parseGvizDate($expiredRaw);
  if (!$expiredDate) continue;

  // LOGIKA NOTIFIKASI
  if ($expiredDate > $limitDate) continue; // Masih lama
  if ($expiredDate < $today) continue;     // Sudah expired

  $expiredFormatted = $expiredDate->format('d F Y');

  $pesan .= "$no. Nomor Sertifikat: $nomor\n";
  $pesan .= "Nama Produk: $namaProduk\n";
  $pesan .= "   Tanggal Expired: $expiredFormatted\n\n";

  $no++;
}

// INI BAGIAN PENYEBAB ERROR 'T' SEBELUMNYA
if ($no === 1) {
  echo 'Tidak ada data sertifikat untuk dikirim';
  return; // Gunakan return agar script pembungkus bisa lanjut
}

if ($dashboard) {
  $pesan .= "🔗 Dashboard:\n$dashboard";
}

// Kirim ke WA lewat Fonnte
$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api.fonnte.com/send",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => [
    'target'  => $target,
    'message' => $pesan
  ],
  CURLOPT_HTTPHEADER => [
    "Authorization: $token"
  ],
]);

$response = curl_exec($curl);
$curl_error = curl_error($curl); // Cek error curl
curl_close($curl);

if ($response === false) {
  echo 'Gagal mengirim pesan WhatsApp: ' . $curl_error;
  return;
}

echo "Laporan berhasil dikirim";
return;

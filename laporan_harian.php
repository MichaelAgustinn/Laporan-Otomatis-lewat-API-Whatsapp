<?php

/*
Lisensi Penggunaan

Hak Cipta (c) 2026 Michael Agustin

Perangkat lunak ini bebas digunakan, disalin, dimodifikasi, dan didistribusikan,
baik untuk tujuan pribadi maupun komersial, dengan ketentuan bahwa pemberitahuan
hak cipta dan lisensi ini tidak boleh dihapus atau dihilangkan dari kode sumber
maupun distribusi perangkat lunak.

Perangkat lunak ini disediakan "sebagaimana adanya", tanpa jaminan apa pun,
baik tersurat maupun tersirat. Penulis tidak bertanggung jawab atas segala
kerugian yang timbul akibat penggunaan perangkat lunak ini.
*/

function parseGvizDate($gvizDate)
{
  if (!is_string($gvizDate)) {
    return null;
  }

  if (preg_match('/Date\((\d+),(\d+),(\d+)\)/', $gvizDate, $m)) {
    $year  = (int) $m[1];
    $month = (int) $m[2] + 1;
    $day   = (int) $m[3];

    return new DateTime("$year-$month-$day");
  }

  return null;
}


// ? baca data dari .env
function loadEnv($path = '.env')
{
  if (!file_exists($path)) {
    die('.env file tidak ditemukan');
  }

  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;

    list($name, $value) = explode('=', $line, 2);
    putenv(trim($name) . '=' . trim($value));
  }
}

loadEnv();

// ? deklarasi variable yg di baca dari .env
$sheetId   = getenv('GOOGLE_SHEET_ID');
$token     = getenv('FONNTE_TOKEN');
$target    = getenv('WHATSAPP_TARGET');
$dashboard = getenv('LOOKER_DASHBOARD_URL');

// ? stop jika env tidak lengkap
if (!$sheetId || !$token || !$target) {
  die('Konfigurasi .env belum lengkap');
}

$url = "https://docs.google.com/spreadsheets/d/$sheetId/gviz/tq?tqx=out:json";

$response = file_get_contents($url);
if ($response === false) {
  die('Gagal mengambil data Google Sheet');
}

$json = json_decode(substr($response, 47, -2), true);

if (!isset($json['table']['rows'])) {
  die('Format data Google Sheet tidak valid');
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

  // ? pengecekan checkbox (harus dicentang)
  if ($notifikasi !== true && $notifikasi !== 'TRUE') {
    continue;
  }

  //? parse tanggal dari Google Sheet
  $expiredDate = parseGvizDate($expiredRaw);
  if (!$expiredDate) continue;

  //? LOGIKA NOTIFIKASI EXPIRED
  if ($expiredDate > $limitDate) continue; // masih lama
  if ($expiredDate < $today) continue;     // sudah expired

  $expiredFormatted = $expiredDate->format('d F Y');

  $pesan .= "$no. Nomor Sertifikat: $nomor\n";
  $pesan .= "Nama Produk: $namaProduk\n";
  $pesan .= "   Tanggal Expired: $expiredFormatted\n\n";

  $no++;
}

if ($no === 1) {
  die('Tidak ada data sertifikat untuk dikirim');
}

if ($dashboard) {
  $pesan .= "🔗 Dashboard:\n$dashboard";
}

// ? kirim ke wa lewat fonnte
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

// ? log gagal atau berhasil
if ($response === false) {
  die('Gagal mengirim pesan WhatsApp');
}

curl_close($curl);

echo "Laporan berhasil dikirim\n";

<?php
require 'auth_check.php';

$envFile = '../.env';
$data = $_POST['env'] ?? [];

$content = "";
foreach ($data as $k => $v) {
  $content .= "$k=$v\n";
}

file_put_contents($envFile, $content);

header('Location: index.php?success=1');

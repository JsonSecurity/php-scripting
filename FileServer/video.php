<?php
if (!isset($_GET['file'])) {
    http_response_code(400);
    exit("Archivo no especificado.");
}

$file = $_GET['file'];

// Seguridad: evitar rutas maliciosas
$file = realpath($file);
$baseDir = realpath(__DIR__); // Evita que se acceda a archivos fuera del directorio actual

if (!$file || strpos($file, $baseDir) !== 0 || !file_exists($file)) {
    http_response_code(404);
    exit("Archivo no encontrado.");
}

$size = filesize($file);
$fp = fopen($file, "rb");

header("Content-Type: video/mp4");
header("Accept-Ranges: bytes");

if (isset($_SERVER['HTTP_RANGE'])) {
    $range = str_replace("bytes=", "", $_SERVER['HTTP_RANGE']);
    $range = explode("-", $range);
    $start = intval($range[0]);
    $end = isset($range[1]) && $range[1] !== "" ? intval($range[1]) : $size - 1;

    if ($start > $end || $end >= $size) {
        http_response_code(416);
        header("Content-Range: bytes */$size");
        exit;
    }

    header("HTTP/1.1 206 Partial Content");
    header("Content-Range: bytes $start-$end/$size");
    fseek($fp, $start);
    $length = $end - $start + 1;
} else {
    $length = $size;
}

header("Content-Length: " . $length);

while (!feof($fp) && $length > 0) {
    echo fread($fp, min(8192, $length));
    $length -= 8192;
    flush();
}
fclose($fp);
exit;
?>

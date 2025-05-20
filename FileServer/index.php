<?php
$mainDir = "."; // Carpeta principal donde están los archivos
$dir = isset($_GET['dir']) ? $_GET['dir'] : $mainDir;

// Mover archivo a la carpeta "trash"
if (isset($_GET['delete'])) {
    $fileToDelete = $_GET['delete'];
    $fileDir = dirname($fileToDelete);
    $trashDir = $fileDir . '/trash';

    if (!file_exists($trashDir)) {
        mkdir($trashDir, 0777, true); // Crear carpeta trash si no existe
    }

    $filename = basename($fileToDelete);
    $destination = $trashDir . '/' . $filename;

    if (file_exists($destination)) {
        $destination = $trashDir . '/' . time() . '_' . $filename;
    }

    rename($fileToDelete, $destination);
    header("Location: index.php?dir=" . urlencode($fileDir));
    exit;
}

function listDirectories($dir) {
    return array_filter(glob($dir . '/*'), 'is_dir');
}

function listFiles($dir, $allowedExtensions) {
    $files = [];
    foreach (glob($dir . '/*') as $file) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $allowedExtensions)) {
            $files[] = $file;
        }
    }
    return $files;
}

$folders = listDirectories($dir);
$images = listFiles($dir, ['jpg', 'jpeg', 'png', 'gif']);
$videos = listFiles($dir, ['mp4', 'webm', 'avi']);
$backLink = ($dir !== $mainDir) ? dirname($dir) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: rgba(28, 40, 51); }
        .container { max-width: 900px; margin: auto; background-color: rgba(44, 62, 80); padding: 20px; }
        .gallery { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }
        .folder, .file { text-align: center; cursor: pointer; }
        .folder img, .file img { width: 100px; height: auto; }
        .image { max-width: 100%; height: auto; cursor: pointer; transition: transform 0.3s ease-in-out; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.9); align-items: center; justify-content: center; }
        .modal img { max-width: 90%; max-height: 90%; transition: transform 0.3s ease-in-out; }
        .close { position: absolute; top: 10px; right: 20px; color: white; font-size: 150px; cursor: pointer; }
        .back { display: block; margin-bottom: 15px; text-decoration: none; color: white; font-weight: bold; }
        a { color: white; text-decoration: none; }
        h1 { color: white; }
    </style>
</head>
<body>

<div class="container">
    <h1>Galería de Archivos</h1>

    <?php if ($backLink): ?>
        <a href="?dir=<?php echo urlencode($backLink); ?>" class="back">⬅ Volver</a>
    <?php endif; ?>

    <div class="gallery">
        <!-- 📂 Carpetas -->
        <?php foreach ($folders as $folder): ?>
            <div class="folder">
                <a href="?dir=<?php echo urlencode($folder); ?>">
                    <img src="https://cdn-icons-png.flaticon.com/128/716/716784.png" alt="Carpeta">
                    <p><?php echo basename($folder); ?></p>
                </a>
            </div>
        <?php endforeach; ?>

        <!-- 🖼️ Imágenes -->
        <?php foreach ($images as $image): ?>
            <div class="file">
                <img src="<?php echo $image; ?>" class="image" onclick="openModal('<?php echo $image; ?>')">
                <br>
                <a href="?delete=<?php echo urlencode($image); ?>&dir=<?php echo urlencode($dir); ?>" onclick="return confirm('¿Mover a trash?')">🗑 Eliminar</a>
            </div>
        <?php endforeach; ?>

        <!-- 🎥 Videos -->
        <?php foreach ($videos as $video): ?>
            <div class="file">
                <video controls width="200">
                    <source src="video.php?file=<?php echo urlencode($video); ?>" type="video/mp4">
                </video>
                <br>
                <a href="?delete=<?php echo urlencode($video); ?>&dir=<?php echo urlencode($dir); ?>" onclick="return confirm('¿Mover a trash?')">🗑 Eliminar</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal para ampliar imágenes -->
<div id="myModal" class="modal" >
    <span class="close" onclick="closeModal()">&times;</span>
    <img id="modalImg" onclick="toggleZoom()">
</div>

<script>
    let zoomed = false;

    function openModal(src) {
        const modalImg = document.getElementById("modalImg");
        modalImg.src = src;
        modalImg.style.transform = "scale(1)";
        zoomed = false;
        document.getElementById("myModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("myModal").style.display = "none";
    }

    function toggleZoom() {
        const modalImg = document.getElementById("modalImg");
        zoomed = !zoomed;
        modalImg.style.transform = zoomed ? "scale(2)" : "scale(1)";
    }
</script>

</body>
</html>

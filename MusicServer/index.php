<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lista de Canciones</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      padding: 40px;
    }

    h1 {
      color: #333;
    }

    .cancion {
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .nombre {
      font-weight: bold;
      color: #555;
      max-width: 70%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .boton-descarga {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s;
    }

    .boton-descarga:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
  <h1>Mis canciones .m4a</h1>

  <?php
    $ruta = __DIR__ . '/musica';
    $archivos = glob($ruta . '/*.m4a');

    if (empty($archivos)) {
      echo "<p>No se encontraron canciones en la carpeta <strong>musica/</strong>.</p>";
    } else {
      foreach ($archivos as $archivo) {
        $nombreVisible = basename($archivo);
        $nombreEscapado = "musica/" . rawurlencode($nombreVisible); // <-- escapado seguro
        echo "
          <div class='cancion'>
            <div class='nombre'>$nombreVisible</div>
            <a href='$nombreEscapado' download>
              <button class='boton-descarga'>Descargar</button>
            </a>
          </div>
        ";
      }
    }
  ?>
</body>
</html>

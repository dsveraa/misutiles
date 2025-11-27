<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Productos - Librería</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="/favicon.png">
    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>

<body>
    <div id="cameraModal">
        <div>
            <video id="videoBarcode"></video>
            <div id="qrPreview"></div>
            <button id="closeCamera">Cerrar</button>
        </div>
    </div>

    <div class="container">
        <?php
        require_once "env.php";

        $editando = false;
        $productoEditar = null;

        if (isset($_GET["editar"])) {
            $editando = true;
            $id = intval($_GET["editar"]);

            $host = getenv("DB_HOST") ?: "localhost";
            $user = getenv("DB_USER") ?: "root";
            $pass = getenv("DB_PASS") ?: "123456";
            $db   = getenv("DB_NAME") ?: "misutiles";
            $port = getenv("DB_PORT") ?: 3306;

            $conn = new mysqli($host, $user, $pass, $db, $port);

            $conn->set_charset("utf8");

            $sql = "SELECT 
                p.id_producto,
                p.codigo_barras,
                p.codigo_qr,
                p.nombre,
                p.imagen_url,
                p.id_tipo_producto,
                i.id_ciudad,
                i.numero_estante,
                i.cantidad_piezas_sueltas,
                i.cantidad_cajas,
                i.piezas_por_caja,
                i.total_piezas
                FROM productos p
                JOIN inventario i ON p.id_producto = i.id_producto
                WHERE p.id_producto = $id";

            $result = $conn->query($sql);
            $productoEditar = $result->fetch_assoc();
        }
        ?>

        <form id="form" action="guardar_producto.php" method="POST" enctype="multipart/form-data">
            <div class="IACC">
                <h1>Taller de integración de Software - IACC</h1>
            </div>
            <div>
                <h2>Registro de Productos</h2>
                <label>Código de barras:</label>
                <input type="text" id="barcode" name="barcode" value="<?= $editando ? $productoEditar['codigo_barras'] : '' ?>" required>
                <button type="button" id="scan">Escanear código</button>
                <video id="preview" style="width:300px; display:none;"></video>
            </div>


            <div>
                <label>Código QR:</label>
                <input type="text" id="codigo_qr" name="codigo_qr" value="<?= $editando ? $productoEditar['codigo_qr'] : '' ?>">
                <button type="button" id="scanBtn">Escanear QR</button>
                <div id="reader" style="width: 300px; margin-top: 20px; display: none;"></div>
                <br>
            </div>

            <div>
                <label>Nombre del producto:</label>
                <input type="text" name="nombre" value="<?= $editando ? $productoEditar['nombre'] : '' ?>" required>
                <br>
            </div>

            <div>
                <label>Imagen del producto:</label>
                <input type="file" id="imagen" accept="image/*">
                <br>
                <div id="preview_container">
                    <img id="img_preview">
                    <div id="checkmark">✔</div>
                </div>
                <button type="button" id="subir">Subir imagen</button>

                <input type="hidden" id="imagen_url" name="imagen_url" value="<?= $editando ? $productoEditar['imagen_url'] : '' ?>">

                <br>
            </div>

            <div>
                <label>Tipo de producto:</label>
                <select name="tipo_producto">
                    <option value="1" <?= ($editando && $productoEditar['id_tipo_producto'] == 1) ? 'selected' : '' ?>>Oficina</option>
                    <option value="2" <?= ($editando && $productoEditar['id_tipo_producto'] == 2) ? 'selected' : '' ?>>Escolar</option>
                    <option value="3" <?= ($editando && $productoEditar['id_tipo_producto'] == 3) ? 'selected' : '' ?>>Mueblería</option>
                </select>
                <br>
            </div>

            <div>
                <label>Ciudad:</label>
                <select name="ciudad">
                    <option value="1" <?= ($editando && $productoEditar['id_ciudad'] == 1) ? 'selected' : '' ?>>Santiago</option>
                    <option value="2" <?= ($editando && $productoEditar['id_ciudad'] == 2) ? 'selected' : '' ?>>Valparaíso</option>
                </select>
                <br>
            </div>

            <div>
                <label>Número de estante (3 dígitos):</label>
                <input id="numero-estante" type="number" name="numero_estante" min="0" value="<?= $editando ? $productoEditar['numero_estante'] : 0 ?>" required>
                <br>
            </div>

            <div>
                <label>Piezas por caja:</label>
                <input id="piezas-por-caja" type="number" name="piezas_por_caja" min="0" value="<?= $editando ? $productoEditar['piezas_por_caja'] : 0 ?>">
                <br>
            </div>

            <div>
                <label>Cantidad de cajas:</label>
                <input id="cantidad-cajas" type="number" name="cantidad_cajas" min="0" value="<?= $editando ? $productoEditar['cantidad_cajas'] : 0 ?>">
                <br>
            </div>

            <div>
                <label>Cantidad de piezas sueltas:</label>
                <input id="cantidad-piezas-sueltas" type="number" name="cantidad_piezas_sueltas" min="0" value="<?= $editando ? $productoEditar['cantidad_piezas_sueltas'] : 0 ?>">
                <br>
            </div>

            <div>
                <label>Total piezas:</label>
                <input type="number" id="total-piezas" disabled value="<?= $editando ? $productoEditar['total_piezas'] : 0 ?>">
                <br>
            </div>

            <button type="submit" id="submitBtn">
                <?= $editando ? "Actualizar" : "Guardar" ?>
            </button>

            <?php if ($editando): ?>
                <input type="hidden" name="id_producto" value="<?= $productoEditar['id_producto'] ?>">
            <?php endif; ?>

        </form>

        <div class="tabla-container">
            <?php
            $host = getenv("DB_HOST") ?: "localhost";
            $user = getenv("DB_USER") ?: "root";
            $pass = getenv("DB_PASS") ?: "123456";
            $db   = getenv("DB_NAME") ?: "misutiles";
            $port = getenv("DB_PORT") ?: 3306;

            $conn = new mysqli($host, $user, $pass, $db, $port);
            
            $conn->set_charset("utf8");

            $sql = "SELECT 
            p.id_producto,
            p.nombre,
            p.codigo_barras,
            p.imagen_url,
            t.nombre_tipo,
            c.nombre_ciudad,
            i.cantidad_piezas_sueltas,
            i.cantidad_cajas,
            i.piezas_por_caja,
            i.total_piezas
            FROM productos p
            JOIN inventario i ON p.id_producto = i.id_producto
            JOIN tipos_producto t ON p.id_tipo_producto = t.id_tipo_producto
            JOIN ciudades c ON i.id_ciudad = c.id_ciudad";

            $result = $conn->query($sql);

            echo "<table>
            <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Código barras</th>
            <th>Tipo</th>
            <th>Ciudad</th>
            <th>Total piezas</th>
            <th>Acciones</th>
            </tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
            <td><img src={$row['imagen_url']}/></td>
            <td>{$row['nombre']}</td>
            <td>{$row['codigo_barras']}</td>
            <td>{$row['nombre_tipo']}</td>
            <td>{$row['nombre_ciudad']}</td>
            <td>{$row['total_piezas']}</td>
            <td>
                <a href='?editar={$row['id_producto']}'>Editar</a> |
                <a href='eliminar.php?id={$row['id_producto']}'>Eliminar</a>
            </td>
            </tr>";
            }

            echo "</table>";

            $conn->close();
            ?>
        </div>
    </div>

    <script src="scanner.js"></script>
    <script type="module" src="imagen.js"></script>
    <script src="piezasTotales.js"></script>
    <script type="module" src="validaciones.js"></script>

</body>

</html>
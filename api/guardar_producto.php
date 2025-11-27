<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once "env.php";

$host = getenv("DB_HOST") ?: "localhost";
$user = getenv("DB_USER") ?: "root";
$pass = getenv("DB_PASS") ?: "123456";
$db   = getenv("DB_NAME") ?: "misutiles";
$port = getenv("DB_PORT") ?: 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id_producto = $_POST["id_producto"] ?? null;
$codigo_barras = $_POST["barcode"] ?? "";
$codigo_qr = $_POST["codigo_qr"] ?? "";
$nombre = $_POST["nombre"] ?? "";
$imagen_url = $_POST["imagen_url"] ?? "";
$id_tipo_producto = $_POST["tipo_producto"] ?? "";
$id_ciudad = $_POST["ciudad"] ?? "";
$numero_estante = $_POST["numero_estante"] ?? 0;
$cantidad_piezas_sueltas = $_POST["cantidad_piezas_sueltas"] ?? 0;
$cantidad_cajas = $_POST["cantidad_cajas"] ?? 0;
$piezas_por_caja = $_POST["piezas_por_caja"] ?? 0;

$conn->begin_transaction();

try {
    if ($id_producto) {
        $sql1 = "UPDATE productos 
                 SET codigo_barras=?, codigo_qr=?, nombre=?, imagen_url=?, id_tipo_producto=? 
                 WHERE id_producto=?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("ssssii", $codigo_barras, $codigo_qr, $nombre, $imagen_url, $id_tipo_producto, $id_producto);
        $stmt1->execute();

        $sql2 = "UPDATE inventario
                 SET id_ciudad=?, numero_estante=?, cantidad_piezas_sueltas=?, cantidad_cajas=?, piezas_por_caja=?
                 WHERE id_producto=?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("isiiii", $id_ciudad, $numero_estante, $cantidad_piezas_sueltas, $cantidad_cajas, $piezas_por_caja, $id_producto);
        $stmt2->execute();

    } else {
        $sql1 = "INSERT INTO productos (codigo_barras, codigo_qr, nombre, imagen_url, id_tipo_producto) VALUES (?,?,?,?,?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("ssssi", $codigo_barras, $codigo_qr, $nombre, $imagen_url, $id_tipo_producto);
        $stmt1->execute();
        $id_producto = $conn->insert_id;

        $sql2 = "INSERT INTO inventario (id_producto, id_ciudad, numero_estante, cantidad_piezas_sueltas, cantidad_cajas, piezas_por_caja) VALUES (?,?,?,?,?,?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("iisiii", $id_producto, $id_ciudad, $numero_estante, $cantidad_piezas_sueltas, $cantidad_cajas, $piezas_por_caja);
        $stmt2->execute();
    }

    $conn->commit();
    header("Location: index.php");

} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();

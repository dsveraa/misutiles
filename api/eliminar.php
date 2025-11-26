<?php
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

$id_producto = $_GET['id'] ?? null;

if ($id_producto) {
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("DELETE FROM inventario WHERE id_producto = ?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();

        $conn->commit();
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error al eliminar: " . $e->getMessage();
    }
}
$conn->close();
?>

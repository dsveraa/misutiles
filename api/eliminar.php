<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "misutiles";

$conn = new mysqli($servername, $username, $password, $dbname);
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

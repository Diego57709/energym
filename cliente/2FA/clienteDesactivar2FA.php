<?php
declare(strict_types=1);
include '../../partials/db.php';

session_start();

// Verifica si el usuario está logueado y es cliente
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || $_SESSION['usuario'] !== 'cliente') {
    header("Location: ../login.php");
    exit;
}

$id_usuario = $_SESSION['id'];

// Usa prepared statements para mayor seguridad
$stmt = $conn->prepare("UPDATE clientes SET google_2fa_secret = NULL WHERE cliente_id = ?");
$stmt->bind_param("i", $id_usuario);

if ($stmt->execute()) {
    echo "<script>
            alert('🚫 2FA ha sido desactivado.');
            window.location.href = '../index.php';
          </script>";
} else {
    echo "<script>
            alert('❌ Error al desactivar 2FA. Inténtalo de nuevo.');
            window.location.href = '../index.php';
          </script>";
}

// Cierra la consulta
$stmt->close();
$conn->close();

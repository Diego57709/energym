<?php
declare(strict_types=1);
include '../../partials/db.php';

session_start();

// Verifica si el usuario está logueado y es trabajador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || $_SESSION['usuario'] !== 'trabajador') {
    header("Location: ../login.php");
    exit;
}

$id_usuario = $_SESSION['id'];

// Usa prepared statements para mayor seguridad
$stmt = $conn->prepare("UPDATE trabajadores SET google_2fa_secret = NULL WHERE trabajador_id = ?");
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

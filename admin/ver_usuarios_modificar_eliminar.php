<?php
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Manager') {
    header('Location: ../index.php?error=permisos_insuficientes');
    exit();
}

include '../partials/db.php';

if (isset($_POST['id'], $_POST['role'])) {
    $id = intval($_POST['id']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Determinar la tabla según el rol
    $tabla = '';
    $columnaId = '';
    if ($role === 'Cliente') {
        $tabla = 'clientes';
        $columnaId = 'cliente_id';
    } elseif ($role === 'Trabajador') {
        $tabla = 'trabajadores';
        $columnaId = 'trabajador_id';
    } elseif ($role === 'Entrenador') {
        $tabla = 'entrenadores';
        $columnaId = 'entrenador_id';
    } else {
        header('Location: ver_usuarios.php?error=rol_invalido');
        exit();
    }

    $query = "DELETE FROM $tabla WHERE $columnaId = $id";

    if (mysqli_query($conn, $query)) {
        header('Location: ver_usuarios.php?success=usuario_eliminado');
    } else {
        header('Location: ver_usuarios.php?error=error_base_datos');
    }
} else {
    header('Location: ver_usuarios.php?error=faltan_datos');
}
mysqli_close($conn);
?>

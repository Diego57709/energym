<?php
ini_set('display_errors', 1); // Activa la visualización de errores
error_reporting(E_ALL); // Reporta todos los errores
session_start();

// Verificar si el usuario tiene rol de Manager
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Manager') {
    header('Location: ../index.php?error=permisos_insuficientes');
    exit();
}

// Incluir la conexión a la base de datos
include '../partials/db.php';

// Verificar si se reciben los parámetros necesarios
if (isset($_POST['id'], $_POST['role'])) {
    $id = intval($_POST['id']); // Asegurar que el ID sea un número entero
    $role = mysqli_real_escape_string($conn, $_POST['role']); // Escapar el rol para evitar inyección SQL

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

    // Construir la consulta para eliminar el usuario
    $query = "DELETE FROM $tabla WHERE $columnaId = $id";

    // Ejecutar la consulta
    if (mysqli_query($conn, $query)) {
        // Eliminación exitosa, redirigir con éxito
        header('Location: ver_usuarios.php?success=usuario_eliminado');
    } else {
        // Error al ejecutar la consulta
        header('Location: ver_usuarios.php?error=error_base_datos');
    }
} else {
    // Redirigir si no se reciben los parámetros
    header('Location: ver_usuarios.php?error=faltan_datos');
}

// Cerrar conexión a la base de datos
mysqli_close($conn);
?>

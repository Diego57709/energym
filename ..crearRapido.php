<?php
// Include database connection file
include 'partials/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



session_start();  // Asegúrate de que la sesión está iniciada

// Datos del administrador
$dni = '12345678A';  // DNI del administrador
$nombre = 'Sigma Boy';
$apellidos = 'Admin Apellido';
$fecha_nacimiento = '1980-01-01';  // Fecha de nacimiento
$direccion = 'Calle Admin 123';  // Dirección
$codigo_postal = '28045';  // Código postal
$telefono = '666868686';  // Teléfono
$email = 'chuhanli2005@gmail.com';  // Correo del administrador
$rol = 'Manager';  // Rol del administrador
$activo = 1;  // Estado activo
$sueldo = 5000.00;  // Sueldo del administrador
$fecha_contratacion = '2025-01-14';  // Fecha de contratación
$password = 'root';  // Contraseña sin cifrar

// Hash de la contraseña antes de insertarla en la base de datos
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insertar los datos del administrador en la tabla "trabajadores"
$sql = "INSERT INTO trabajadores 
        (dni, nombre, apellidos, fecha_nacimiento, direccion, codigo_postal, telefono, email, rol, activo, sueldo, fecha_contratacion, password) 
        VALUES 
        ('$dni', '$nombre', '$apellidos', '$fecha_nacimiento', '$direccion', '$codigo_postal', '$telefono', '$email', '$rol', '$activo', '$sueldo', '$fecha_contratacion', '$hashedPassword')";

// Ejecutar la consulta
if (mysqli_query($conn, $sql)) {
    echo "Nuevo administrador creado con éxito.";
} else {
    echo "Error al crear administrador: " . mysqli_error($conn);
}

?>

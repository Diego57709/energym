<?php
// Include database connection file
include 'partials/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();  // Ensure session is started

// Datos del entrenador
$entrenador_id = null;  // Set to auto-increment if the column is auto-generated
$dni = '87654321B';
$nombre = 'John Doe'; // Updated name
$apellidos = 'Smith';
$fecha_nacimiento = '1990-05-20';
$calle = 'Calle Fitness 456';
$codigo_postal = '28046';
$email = 'johndoe@gmail.com';
$telefono = '777123456';
$especialidad = 'Cardio Training';  // Updated specialty
$reset_token = null;  // Set to NULL if unused
$qr_token = null;  // Set to NULL if unused

// Prepare the SQL statement for insertion
$sql = "INSERT INTO entrenadores 
        (dni, nombre, apellidos, fecha_nacimiento, calle, codigo_postal, email, telefono, especialidad, reset_token, qr_token) 
        VALUES 
        ('$dni', '$nombre', '$apellidos', '$fecha_nacimiento', '$calle', '$codigo_postal', '$email', '$telefono', '$especialidad', '$reset_token', '$qr_token')";

// Execute the query
if (mysqli_query($conn, $sql)) {
    echo "Nuevo entrenador creado con éxito.";
} else {
    echo "Error al crear entrenador: " . mysqli_error($conn);
}
?>

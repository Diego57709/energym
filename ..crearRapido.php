<?php
// Include database connection file
include 'partials/db.php';

// Set up the worker's details
$trabajador_id = '12345678A';  // Example DNI
$nombre = 'Sigma Boy';
$telefono = '666868686';  // Example phone number
$email = 'sigmaboy@pene.com';
$rol = 'Manager';  // Worker role
$activo = 1;  // Active status
$suelo = 1.00;  // Salary
$fecha_contratacion = '2025-01-14';  // Hiring date
$password = 'root';  // Raw password

// Hash the password before storing it
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert the worker's details into the database
$sql = "INSERT INTO trabajadores 
        (dni, nombre, telefono, email, rol, activo, sueldo, fecha_contratacion, password) 
        VALUES 
        ('$trabajador_id', '$nombre', '$telefono', '$email', '$rol', '$activo', '$suelo', '$fecha_contratacion', '$hashedPassword')";

// Execute the query
if (mysqli_query($conn, $sql)) {
    echo "New trabajador created successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>

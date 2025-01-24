<?php
declare(strict_types=1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Configuración de la conexión a la base de datos
$host = 'localhost';  // Cambia si usas otro host
$user = 'root';       // Cambia según tu configuración
$password = 'rootroot';       // Cambia si tu usuario tiene contraseña
$database = 'gimnasiodb'; // Cambia por el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener los datos de la tabla `clientes`
$sql = "SELECT cliente_id, nombre, apellidos, email FROM clientes LIMIT 10";
$result = $conn->query($sql);

// Verificar si se recuperaron filas
if ($result && $result->num_rows > 0) {
    echo "<h3>Resultados de la tabla `clientes`:</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Apellidos</th><th>Email</th></tr>";

    // Iterar sobre los resultados y mostrarlos
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['cliente_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($row['apellidos']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "No se encontraron resultados en la tabla `clientes`.";
}

// Cerrar conexión
$conn->close();
?>

<?php 
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include '../partials/db.php';
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || ($_SESSION['usuario'] !== 'entrenador')) {
    header("Location: /login.php");
    exit();
}
if (time() > $_SESSION['timeout']) {
    session_unset(); 
    session_destroy();
    header('Location: /login.html');
    exit();
}

$usuario = $_SESSION['usuario'];
$id_entrenador = $_SESSION['id'];

$sqlClasesFuturas = "
    SELECT 
        cg.clase_id,
        cg.nombre_clase,
        cg.capacidad,
        cg.fecha_hora_c,
        cg.fecha_hora_f,
        COUNT(ci.cliente_id) AS inscritos,
        GROUP_CONCAT(CONCAT(c.apellidos, ' ', c.nombre, ' (', c.email, ')') SEPARATOR '|') AS lista_inscritos
    FROM clases_grupales cg
    LEFT JOIN clases_inscripciones ci ON cg.clase_id = ci.clase_id
    LEFT JOIN clientes c ON ci.cliente_id = c.cliente_id
    WHERE cg.entrenador_id = ?
    AND DATE(cg.fecha_hora_c) >= CURDATE()
    GROUP BY cg.clase_id
    ORDER BY cg.fecha_hora_c ASC
";

$stmt = mysqli_prepare($conn, $sqlClasesFuturas);
mysqli_stmt_bind_param($stmt, "i", $id_entrenador);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$clasesFuturas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Clases | EnerGym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .main-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .table-container {
            width: 100%;
            max-width: 900px;
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #0d6efd;
            color: white;
            text-align: center;
        }
        .table td {
            text-align: center;
            vertical-align: middle;
        }
        .btn {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Encabezado -->
    <?php require '../partials/header1.view.php'; ?>

    <!-- Sección de contenido -->
    <div class="content">
        <div class="table-container">
            <h2 class="text-center mb-4">Mis Clases Grupales</h2>
            
            <!-- Clases futuras del entrenador -->
            <h2 class="text-center mt-4">Próximas Clases</h2>
            <?php if (count($clasesFuturas) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Clase</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Capacidad</th>
                                <th>Inscritos</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clasesFuturas as $clase): ?>
                                <tr>
                                    <td><?= htmlspecialchars($clase['nombre_clase']) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_c'])) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_f'])) ?></td>
                                    <td><?= $clase['capacidad'] ?></td>
                                    <td><?= $clase['inscritos'] ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm ver-inscritos-btn"
                                            data-clase="<?= htmlspecialchars($clase['nombre_clase']) ?>"
                                            data-inscritos="<?= htmlspecialchars($clase['lista_inscritos'] ?? '') ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalInscritos">
                                            <i class="fas fa-eye"></i> Ver Inscritos
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No tienes clases programadas.</p>
            <?php endif; ?>

            <!-- Botón para volver -->
            <div class="d-flex justify-content-center mt-4">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Pie de página -->
    <?php require '../partials/footer.view.php'; ?>
</div>

<!-- Modal para mostrar inscritos -->
<div class="modal fade" id="modalInscritos" tabindex="-1" aria-labelledby="modalInscritosLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInscritosLabel">Alumnos Inscritos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="modalClaseNombre"></h6>
                <ul id="listaInscritos" class="list-group">
                    <li class="list-group-item text-center">Selecciona una clase...</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".ver-inscritos-btn").forEach(button => {
        button.addEventListener("click", function () {
            const claseNombre = this.getAttribute("data-clase");
            const inscritosString = this.getAttribute("data-inscritos");
            const listaInscritos = document.getElementById("listaInscritos");
            const modalClaseNombre = document.getElementById("modalClaseNombre");

            modalClaseNombre.innerHTML = `<strong>Clase:</strong> ${claseNombre}`;

            // Si no hay inscritos, mostrar mensaje
            if (!inscritosString) {
                listaInscritos.innerHTML = '<li class="list-group-item text-center">No hay alumnos inscritos.</li>';
                return;
            }

            // Convertir la cadena separada por | en una lista
            const inscritosArray = inscritosString.split("|");
            listaInscritos.innerHTML = "";
            inscritosArray.forEach(inscrito => {
                listaInscritos.innerHTML += `<li class="list-group-item">${inscrito}</li>`;
            });
        });
    });
});
</script>

</body>
</html>

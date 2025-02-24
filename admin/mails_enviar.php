<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enviar correos</title>

  <!-- Bootstrap 5 CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
  
  <!-- Font Awesome -->
  <link 
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
  >
  
  <!-- Summernote CSS (Bootstrap 5 compatible) -->
  <link 
    href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" 
    rel="stylesheet"
  >

  <style>
    body, html {
      height: 100%;
      margin: 0;
    }
    .wrapper {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .main-content {
      flex: 1;
      padding: 40px;
      background-color: #f8f9fa;
      height: 100%;
    }
    .sidebar {
      width: 250px;
      background-color: #343a40;
      color: white;
      padding: 20px;
    }
    .sidebar a {
      color: white;
      display: block;
      padding: 10px 20px;
      border-radius: 5px;
      margin-bottom: 10px;
      text-decoration: none;
    }
    .main-content {
        padding: 40px;
        background-color: #f8f9fa;
    }

    .sidebar a:hover {
      background-color: #495057;
    }
    .active-link {
      background-color: #0d6efd;
      color: white !important;
    }
    .form-section {
      max-width: 800px;
      margin: auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    footer, footer p {
        text-align: center;
        padding: 15px;
        background-color: #343a40;
        color: white;
        }
  </style>
</head>
<body>

<div class="wrapper">
  <div class="d-flex flex-grow-1">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-header">Panel de Control</div>
      <hr>
      <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="ver_usuarios.php"><i class="fas fa-users"></i> Ver Usuarios</a>
      <a href="ver_pagos.php"><i class="fas fa-credit-card"></i> Ver pagos</a>
      <a href="ver_asistencias.php"><i class="fas fa-door-open"></i> Ver asistencias</a>
      <a href="mails_enviar.php" class="active-link"><i class="fas fa-envelope"></i> Enviar correos</a>
      <a href="ver_clases.php"><i class="fas fa-bicycle"></i> Ver clases</a>
      <a href="/trabajador/"><i class="fas fa-sign-out-alt"></i> Salir</a>
      <a href="../logoutProcesar.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main content -->
    <div class="flex-grow-1">
      <div class="main-content">
        <h1 class="mb-4">Enviar correos</h1>

        <div class="form-section">
          <form action="mails_procesar.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="titulo" class="form-label">Título del correo</label>
              <input 
                type="text" 
                name="titulo" 
                id="titulo" 
                class="form-control" 
                placeholder="Título del correo" 
                required
              >
            </div>
            <div class="mb-3">
              <label for="mensaje" class="form-label">Mensaje del correo</label>
              <textarea id="summernote" name="mensaje" class="form-control"></textarea>

            </div>
            <div class="mb-3">
              <label for="destinatarios" class="form-label">Destinatarios</label>
              <select 
                name="destinatarios" 
                id="destinatarios" 
                class="form-control" 
                required
              >
                <option value="todos">Todos</option>
                <option value="clientes">Clientes</option>
                <option value="trabajadores">Trabajadores</option>
                <option value="entrenadores">Entrenadores</option>
                <option value="newsletter">Usuarios de la newsletter</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="text-center">
  <p>&copy; 2025 EnerGym Admin Dashboard. All rights reserved.</p>
</footer>

<!-- Bootstrap 5 JS -->
<script 
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
></script>

<!-- jQuery -->
<script 
  src="https://code.jquery.com/jquery-3.6.4.min.js"
></script>

<!-- Summernote JS (Bootstrap 5 compatible) -->
<script 
  src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"
></script>

<script>
  $(document).ready(function() {
    $('#summernote').summernote({
      placeholder: 'Escribe el contenido del correo aquí...',
      tabsize: 2,
      height: 300,
      toolbar: [
        ['font', ['bold', 'italic', 'underline', 'clear']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['view', ['fullscreen', 'codeview', 'help']]
      ]
    });
  });
</script>

</body>
</html>

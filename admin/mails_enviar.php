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
            
            <!-- Select de presets -->
            <div class="mb-3">
              <label for="preset" class="form-label">Selecciona un preset</label>
              <select id="preset" class="form-control">
                <option value="">-- Seleccione un preset --</option>
                <option value="bienvenida">Correo de Bienvenida</option>
                <option value="promocion">Promoción Especial</option>
                <option value="recordatorio">Recordatorio de Clase</option>
                <option value="cumpleanos">Feliz Cumpleaños</option>
                <option value="evento">Invitación a Evento</option>
                <option value="actualizacion">Actualización de Servicios</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="mensaje" class="form-label">Mensaje del correo</label>
              <textarea id="summernote" name="mensaje" class="form-control"></textarea>
            </div>
            
            <!-- Checkboxes para destinatarios por grupo -->
            <div class="mb-3">
              <label class="form-label">Destinatarios por grupo</label>
              <div class="form-check">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  name="destinatarios_grupo[]" 
                  value="clientes" 
                  id="destinatarioClientes"
                >
                <label class="form-check-label" for="destinatarioClientes">Clientes</label>
              </div>
              <div class="form-check">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  name="destinatarios_grupo[]" 
                  value="trabajadores" 
                  id="destinatarioTrabajadores"
                >
                <label class="form-check-label" for="destinatarioTrabajadores">Trabajadores</label>
              </div>
              <div class="form-check">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  name="destinatarios_grupo[]" 
                  value="entrenadores" 
                  id="destinatarioEntrenadores"
                >
                <label class="form-check-label" for="destinatarioEntrenadores">Entrenadores</label>
              </div>
              <div class="form-check">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  name="destinatarios_grupo[]" 
                  value="newsletter" 
                  id="destinatarioNewsletter"
                >
                <label class="form-check-label" for="destinatarioNewsletter">Usuarios de la newsletter</label>
              </div>
            </div>
            
            <!-- Campo para ingresar destinatarios individuales -->
            <div class="mb-3">
              <label for="destinatarios_individuales" class="form-label">
                Destinatarios individuales (ingresa uno o varios emails, separados por comas)
              </label>
              <input 
                type="text" 
                class="form-control" 
                id="destinatarios_individuales" 
                name="destinatarios_individuales" 
                placeholder="correo1@ejemplo.com, correo2@ejemplo.com"
              >
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

    // Presets de correos: se asignan tanto título como mensaje
    $('#preset').on('change', function() {
      var preset = $(this).val();
      if (preset === 'bienvenida') {
        $('#titulo').val('Bienvenido a EnerGym');
        $('#summernote').summernote('code', '<p>Hola,</p><p>¡Bienvenido a EnerGym! Nos alegra contar contigo. Explora nuestras instalaciones y servicios diseñados para ti.</p><p>Saludos,</p><p>El equipo de EnerGym</p>');
      } else if (preset === 'promocion') {
        $('#titulo').val('Promoción Especial en EnerGym');
        $('#summernote').summernote('code', '<p>¡No te pierdas nuestra promoción especial!</p><p>Durante este mes, disfruta de descuentos exclusivos en nuestros servicios. ¡Te esperamos!</p>');
      } else if (preset === 'actualizacion') {
        $('#titulo').val('Actualización de Servicios en EnerGym');
        $('#summernote').summernote('code', '<p>Hola,</p><p>Tenemos importantes actualizaciones en nuestros servicios. Por favor, revisa los detalles y mantente informado.</p><p>Gracias,</p><p>El equipo de EnerGym</p>');
      } else {
        $('#titulo').val('');
        $('#summernote').summernote('code', '');
      }
    });
  });
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EnerGym</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .header-bar {
      background-color: rgb(41, 48, 53);
      border-bottom: 5px solid rgb(15, 139, 141);
    }
    .header-icon {
      width: 8rem;
      padding: 1.25rem;
      transition: transform 0.3s ease;
    }
    .header-icon:hover {
      transform: scale(1.05);
    }
    .header-cliente {
      font-size: 2rem; /* Texto más grande */
      color: white;
      text-transform: uppercase;
      background-color: rgb(15, 139, 141);
      padding: 0.75rem 1.5rem;
      border-radius: 5px;
      font-weight: bold;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .header-cliente:hover {
      background-color: rgb(10, 94, 95);
    }
    .dropdown-icon {
      width: 8rem;
      padding: 1rem;
      transition: transform 0.3s ease;
    }
    .dropdown-icon:hover {
      transform: scale(1.05);
    }
    /* Estilos para el menú offcanvas sin fade ni fondo oscuro */
    .offcanvas {
      z-index: 1055;
      width: 350px; /* Menú más ancho */
      background-color: #fff;
      box-shadow: -4px 0 8px rgba(0, 0, 0, 0.1);
      border-left: 1px solid #ddd;
      border-top-left-radius: 8px;
      border-bottom-left-radius: 8px;
      transition: none;
    }
    .offcanvas .offcanvas-header {
      border-bottom: 1px solid #ddd;
      background-color: #f8f9fa;
    }
    .offcanvas .offcanvas-body {
      padding: 1rem 1.5rem;
      display: flex;
      flex-direction: column;
      min-height: 300px;
    }
    .offcanvas a.dropdown-item {
      color: #333;
      font-size: 1.5rem;
      font-weight: bold;
      transition: color 0.3s ease, background-color 0.3s ease;
      padding: 0.75rem 1.25rem;
      border-radius: 4px;
      margin-bottom: 0.5rem;
    }
    .offcanvas a.dropdown-item:hover {
      color: rgb(15, 139, 141);
      background-color: rgba(15, 139, 141, 0.1);
      text-decoration: none;
    }
    /* Estilos para el botón "Inscríbete" (mismo estilo que Área Cliente) */
    .inscribete-btn a {
      display: block;
      text-align: center;
      font-size: 2rem; /* Texto más grande */
      font-weight: bold;
    }
    /* Estilos para el pie del menú lateral */
    .offcanvas-footer {
      text-align: center;
      padding-top: 1rem;
      border-top: 1px solid #ddd;
      font-size: 0.9rem;
      color: #555;
    }
    @media (max-width: 768px) {
      .header-icon {
        width: 5rem;
      }
      .header-cliente {
        font-size: 1.5rem;
        padding: 0.5rem 1rem;
      }
      .dropdown-icon {
        width: 5rem;
      }
      .inscribete-btn a {
        font-size: 1.5rem;
      }
      .offcanvas a.dropdown-item {
        font-size: 1.3rem;
      }
    }
    @media (max-width: 480px) {
      .header-cliente {
        font-size: 1.2rem;
        padding: 0.4rem 0.8rem;
      }
      .dropdown-icon {
        width: 4rem;
      }
      .inscribete-btn a {
        font-size: 1.2rem;
      }
      .offcanvas a.dropdown-item {
        font-size: 1.1rem;
      }
    }
  </style>
</head>
<body>
  <header class="header-bar d-flex align-items-center px-3 py-2">
    <!-- Izquierda: Logo -->
    <div class="flex-shrink-0">
      <a href="/" class="d-flex align-items-center">
        <img src="/partials/img/icon.webp" alt="Logo" class="header-icon">
      </a>
    </div>
    
    <!-- Centro: Área Cliente -->
    <div class="flex-grow-1 text-center">
      <a href="/login.php" class="header-cliente text-decoration-none">Área Cliente</a>
    </div>
    
    <!-- Derecha: Botón para abrir el menú lateral -->
    <div class="flex-shrink-0">
      <button class="btn p-0 border-0" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
        <img src="/img/nav.webp" alt="Navigation" class="dropdown-icon">
      </button>
    </div>
  </header>

  <!-- Menú lateral (Offcanvas) sin fade, sin fondo oscuro y permitiendo scroll -->
  <div class="offcanvas offcanvas-end" data-bs-backdrop="false" data-bs-scroll="true" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasMenuLabel">Energym</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
      <!-- Opciones de menú -->
      <div>
        <ul class="list-unstyled">
          <li><a class="dropdown-item" href="/index.php">Home</a></li>
          <li><a class="dropdown-item" href="/nosotros.php">Sobre Nosotros</a></li>
          <li><a class="dropdown-item" href="/contactanos.php">Contáctanos</a></li>
          <li><a class="dropdown-item" href="/faq.php">FAQ</a></li>
        </ul>
      </div>
      <!-- Grupo final: el botón "Inscríbete" y, debajo, el footer -->
      <div class="mt-auto">
        <div class="inscribete-btn mb-3">
          <a href="/planes.php" class="header-cliente text-decoration-none">Inscríbete</a>
        </div>
        <div class="offcanvas-footer">
          <p>© 2024 EnerGym. All Rights Reserved.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

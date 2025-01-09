<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnerGym</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header-bar {
            background-color: rgb(41, 48, 53);
            border-bottom: 5px solid rgb(15, 139, 141);
        }
        .header-icon {
            width: 6.75rem;
            padding: 1.25rem;
        }
        .header-cliente {
            font-size: 1.5rem;
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
            width: 6rem;
            padding: 1rem;
        }
        .dropdown-menu {
            margin-top: 10px !important;
        }
        @media (max-width: 768px) {
            .header-icon {
                width: 5rem;
            }
            .header-cliente {
                font-size: 1.2rem;
                padding: 0.5rem 1rem;
            }
            .dropdown-icon {
                width: 5rem;
            }
            .dropdown-menu {
                width: 100vw;
                left: 0;
                right: 0;
                padding: 0;
                border-radius: 0;
            }
            .dropdown-menu .dropdown-item {
                display: block;
                text-align: center;
                padding: 1rem;
            }
        }
        @media (max-width: 480px) {
            .header-cliente {
                font-size: 1rem;
                padding: 0.4rem 0.8rem;
            }
            .dropdown-icon {
                width: 4rem;
                
            }
        }
    </style>
</head>
<body>
    <header class="header-bar d-flex align-items-center justify-content-between px-3 py-2">
        <!-- Logo -->
        <a href="/" class="d-flex align-items-center">
            <img src="img/icon.webp" alt="Logo" class="header-icon">
        </a>

        <!-- Area Cliente -->
        <a href="login.php" class="header-cliente text-decoration-none">
            Area Cliente
        </a>

        <!-- Dropdown Menu -->
        <div class="dropdown">
            <button class="btn p-0 border-0 dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="img/nav.webp" alt="Navigation" class="dropdown-icon">
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="/index.php">Home</a></li>
                <li><a class="dropdown-item" href="/nosotros.php">Sobre Nosotros</a></li>
                <li><a class="dropdown-item" href="/contactanos.php">Contactanos</a></li>
                <li><a class="dropdown-item" href="/faq.php">FAQ</a></li>
            </ul>
        </div>
    </header>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

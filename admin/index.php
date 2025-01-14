<?php
// Simple login validation (this is a basic example, do not use in production)
session_start();
if (!isset($_SESSION['rol']) == 'Manager') {
    header('Location: trabajador.php?error=permisos_insuficientes');
}

// Admin dashboard content
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        nav {
            background-color: #444;
            padding: 10px;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .content {
            margin-top: 20px;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
        }
    </style>
</head>
<body>

<header>
    <h1>Admin Dashboard</h1>
</header>

<nav>
    <a href="admin.php">Dashboard</a>
    <a href="settings.php">Settings</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <div class="content">
        <h2>Welcome to the Admin Panel</h2>
        <p>This is a simple admin page. You can add more features like managing users, content, etc.</p>
        <h3>Admin Actions</h3>
        <ul>
            <li><a href="#">View Users</a></li>
            <li><a href="#">Manage Posts</a></li>
            <li><a href="#">Site Settings</a></li>
        </ul>
    </div>
</div>

<footer>
    <p>&copy; 2025 Your Website. All rights reserved.</p>
</footer>

</body>
</html>

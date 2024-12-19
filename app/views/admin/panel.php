<?php
require_once __DIR__ . '\\..\\..\\config\\Database.php';
require_once __DIR__ . '\\..\\..\\models\\User.php';
require_once __DIR__ . '\\..\\..\\controllers\\UserController.php';
require_once __DIR__ . '\\..\\..\\utils\\Auth.php';

use config\Database;
use controllers\UserController; // Ensure this line is included
use models\User;
use utils\Auth;

if (!Auth::isLoggedIn() || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$db = new Database();
$userModel = new User($db->getConnection());
$userController = new UserController($userModel);
$users = $userController->getAllUsers();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto p-4">
        <nav class="flex items-center justify-between mb-6 p-4 bg-gray-700 rounded-lg shadow-lg">
            <div class="flex items-center space-x-4">
                <img src="/2DAW/m7blog/app/public/img/logo.png" alt="Logo" class="h-10">
                <a href="/2DAW/m7blog/app/views/home/index.php" class="text-2xl font-bold text-white hover:text-gray-300">Blog de Videojuegos</a>
            </div>
            <ul class="flex space-x-4">
                <li><a href="/2DAW/m7blog/app/views/home/index.php" class="text-white hover:text-gray-300">Inicio</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="/2DAW/m7blog/app/views/user/profile.php" class="text-white hover:text-gray-300">Perfil</a></li>
                    <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'writer'): ?>
                        <li><a href="/2DAW/m7blog/app/views/post/create.php" class="text-white hover:text-gray-300">Crear Post</a></li>
                    <?php endif; ?>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <li><a href="/2DAW/m7blog/app/views/admin/panel.php" class="text-white hover:text-gray-300">Panel de Control</a></li>
                        <li><a href="/2DAW/m7blog/app/views/admin/help_tickets.php" class="text-white hover:text-gray-300">Tickets de Ayuda</a></li>
                    <?php endif; ?>
                    <li><a href="/2DAW/m7blog/app/views/auth/logout.php" class="text-white hover:text-gray-300">Cerrar sesión</a></li>
                <?php endif; ?>
                <li><a href="/2DAW/m7blog/app/views/help/contact.php" class="text-white hover:text-gray-300">Contactar</a></li>
            </ul>
        </nav>

        <h1 class="text-4xl font-bold mb-4">Panel de Control de Usuarios</h1>
        <p class="mb-6">Administrar usuarios registrados en el blog.</p>

        <?php if (!empty($users)): ?>
            <table class="min-w-full bg-gray-800">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-700">ID</th>
                        <th class="py-2 px-4 border-b border-gray-700">Nombre de Usuario</th>
                        <th class="py-2 px-4 border-b border-gray-700">Correo Electrónico</th>
                        <th class="py-2 px-4 border-b border-gray-700">Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-700"><?php echo htmlspecialchars($user['id']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-700"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-700"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-700"><?php echo htmlspecialchars($user['role']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay usuarios registrados.</p>
        <?php endif; ?>
    </div>
</body>
</html>

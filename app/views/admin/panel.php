<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '\\..\\..\\config\\Database.php';
require_once __DIR__ . '\\..\\..\\models\\User.php';
require_once __DIR__ . '\\..\\..\\controllers\\UserController.php';
require_once __DIR__ . '\\..\\..\\utils\\Auth.php';

use config\Database;
use controllers\UserController;
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id']) && isset($_POST['role'])) {
        $userController->updateUserRole($_POST['user_id'], $_POST['role']);
        header('Location: panel.php');
        exit();
    } elseif (isset($_POST['delete_user_id'])) {
        $userController->deleteUser($_POST['delete_user_id']);
        header('Location: panel.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .help-tickets-btn {
            display: block;
            background-color: #e3342f;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            margin-top: 1rem;
            text-align: right;
            width: fit-content;
            float: right;
        }
        .help-tickets-btn:hover {
            background-color: #cc1f1a;
        }
        .delete-btn {
            background-color: #e3342f;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
        }
        .delete-btn:hover {
            background-color: #cc1f1a;
        }
        .background-image {
            background-image: url('/2DAW/m7blog/app/public/img/background.png');
            background-size: cover;
            background-position: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    <div class="background-image"></div>
    <div class="relative z-10 container mx-auto p-4 bg-gray-900 bg-opacity-85">
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
                    <?php endif; ?>
                    <li><a href="/2DAW/m7blog/app/views/auth/logout.php" class="text-white hover:text-gray-300">Cerrar sesión</a></li>
                <?php endif; ?>
                <li><a href="/2DAW/m7blog/app/views/help/contact.php" class="text-white hover:text-gray-300">Contactar</a></li>
            </ul>
        </nav>
        <a href="/2DAW/m7blog/app/views/admin/help_tickets.php" class="help-tickets-btn">Tickets de Ayuda</a>

        <h1 class="text-4xl font-bold mb-4">Panel de Control</h1>
        <h2 class="text-2xl font-bold mb-4">Usuarios</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-800 rounded-lg shadow-lg">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-300">ID</th>
                        <th class="px-4 py-2 text-left text-gray-300">Nombre de Usuario</th>
                        <th class="px-4 py-2 text-left text-gray-300">Correo Electrónico</th>
                        <th class="px-4 py-2 text-left text-gray-300">Rol</th>
                        <th class="px-4 py-2 text-left text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="bg-gray-700">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($user['id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="px-4 py-2">
                                <form method="POST" action="">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="role" class="bg-gray-600 text-white rounded-md">
                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        <option value="writer" <?php echo $user['role'] === 'writer' ? 'selected' : ''; ?>>Writer</option>
                                        <option value="subscriber" <?php echo $user['role'] === 'subscriber' ? 'selected' : ''; ?>>Subscriber</option>
                                    </select>
                            </td>
                            <td class="px-4 py-2">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Actualizar</button>
                                </form>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="delete-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H3a1 1 0 100 2h1v9a2 2 0 002 2h8a2 2 0 002-2V6h1a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm3 3a1 1 0 112 0v1h-2V5zm-3 3a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (isset($_SESSION['user'])): ?>
        <script>
            console.log("Usuario logueado: <?php echo htmlspecialchars($_SESSION['user']['username']); ?>");
            console.log("Rol del usuario: <?php echo htmlspecialchars($_SESSION['user']['role']); ?>");
        </script>
    <?php endif; ?>
</body>
</html>

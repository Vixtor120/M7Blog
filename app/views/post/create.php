<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Post.php';
require_once __DIR__ . '/../../controllers/PostController.php';
require_once __DIR__ . '/../../utils/Auth.php';

use config\Database;
use utils\Auth;

if (!Auth::isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit();
}

$db = new Database();
$postModel = new \models\Post($db->getConnection());
$postController = new \controllers\PostController($postModel);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $postController->createPost($_POST['title'], $_POST['content'], $_SESSION['user']['id']);
    if ($postId) {
        header("Location: details.php?post_id=$postId");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Post de Videojuegos</title>
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
                    <?php endif; ?>
                    <li><a href="/2DAW/m7blog/app/views/auth/logout.php" class="text-white hover:text-gray-300">Cerrar sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <h1 class="text-4xl font-bold mb-4">Crear Nuevo Post de Videojuegos</h1>
        <form method="POST" action="" class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-300">Título:</label>
                <input type="text" id="title" name="title" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-300">Contenido:</label>
                <textarea id="content" name="content" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md" required></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" name="action" value="create_post" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Crear Post</button>
            </div>
        </form>
    </div>
</body>
</html>
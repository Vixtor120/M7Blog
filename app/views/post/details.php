<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/Database.php';
require_once '../../models/Post.php';
require_once '../../controllers/PostController.php';

use config\Database;

$db = new Database();
$postModel = new \models\Post($db->getConnection());
$postController = new \controllers\PostController($postModel);

if (isset($_GET['post_id'])) {
    $post = $postController->getPostById($_GET['post_id']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Post</title>
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

        <?php if (isset($post)): ?>
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <h1 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($post['title']); ?></h1>
                <p class="mb-4"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                <small class="block text-gray-400">Escrito por: <?php echo htmlspecialchars($post['author']); ?></small>
            </div>
        <?php else: ?>
            <p class="text-red-500">Post no encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Post.php';
require_once __DIR__ . '/../../controllers/PostController.php';
require_once __DIR__ . '/../../utils/Auth.php';

use config\Database;

$db = new Database();
$postModel = new \models\Post($db->getConnection());
$postController = new \controllers\PostController($postModel);
$posts = $postController->getAllPosts();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameVerse</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto p-4">
        <nav class="flex items-center justify-between mb-6 p-4 bg-gray-700 rounded-lg shadow-lg">
            <div class="flex items-center space-x-4">
                <img src="/2DAW/m7blog/app/public/img/logo.png" alt="Logo" class="h-10">
                <a href="/2DAW/m7blog/app/views/home/index.php" class="text-2xl font-bold text-white hover:text-gray-300">GameVerse</a>
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

        <h1 class="text-4xl font-bold mb-4">
            Bienvenido al Blog de Videojuegos
            <?php if (isset($_SESSION['user'])): ?>
                , <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
            <?php endif; ?>
        </h1>
        <p class="mb-6">Esta es la página principal del blog de videojuegos.</p>

        <h2 class="text-2xl font-bold mb-4">Posts Recientes</h2>
        <?php if (!empty($posts)): ?>
            <ul class="list-disc pl-5">
                <?php foreach ($posts as $post): ?>
                    <li class="mb-2">
                        <a href="/2DAW/m7blog/app/views/post/details.php?post_id=<?php echo $post['id']; ?>" class="text-blue-500 hover:underline">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay posts disponibles.</p>
        <?php endif; ?>
    </div>
    <?php if (isset($_SESSION['user'])): ?>
        <script>
            console.log("Usuario logueado: <?php echo htmlspecialchars($_SESSION['user']['username']); ?>");
            console.log("Rol del usuario: <?php echo htmlspecialchars($_SESSION['user']['role']); ?>");
        </script>
    <?php endif; ?>
</body>
</html>
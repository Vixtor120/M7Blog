<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '\\..\\..\\config\\Database.php';
require_once __DIR__ . '\\..\\..\\models\\Post.php';
require_once __DIR__ . '\\..\\..\\controllers\\PostController.php';
require_once __DIR__ . '\\..\\..\\utils\\Auth.php';

use config\Database;
use controllers\PostController;
use models\Post;
use utils\Auth;

if (!Auth::isLoggedIn() || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$db = new Database();
$postModel = new Post($db->getConnection());
$postController = new PostController($postModel);
$posts = $postController->getAllPosts();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_post_id'])) {
        $postController->deletePost($_POST['delete_post_id']);
        header('Location: posts.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controlar Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
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

        <h1 class="text-4xl font-bold mb-4">Controlar Posts</h1>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-800 rounded-lg shadow-lg">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-300">ID</th>
                        <th class="px-4 py-2 text-left text-gray-300">Título</th>
                        <th class="px-4 py-2 text-left text-gray-300">Autor</th>
                        <th class="px-4 py-2 text-left text-gray-300">Fecha</th>
                        <th class="px-4 py-2 text-left text-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr class="bg-gray-700">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($post['id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($post['title']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($post['author'] ?? 'Desconocido'); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($post['created_at']); ?></td>
                            <td class="px-4 py-2">
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="delete_post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('¿Está seguro de que desea borrar este post?');">
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

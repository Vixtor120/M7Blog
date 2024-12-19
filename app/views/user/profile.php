<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '\\..\\..\\config\\Database.php';
require_once __DIR__ . '\\..\\..\\models\\User.php';
require_once __DIR__ . '\\..\\..\\controllers\\UserController.php';
require_once __DIR__ . '\\..\\..\\models\\Post.php';
require_once __DIR__ . '\\..\\..\\controllers\\PostController.php';
require_once __DIR__ . '\\..\\..\\models\\Comment.php';
require_once __DIR__ . '\\..\\..\\controllers\\CommentController.php';

use config\Database;

$db = new Database();
$userModel = new \models\User($db->getConnection());
$userController = new \controllers\UserController($userModel);
$postModel = new \models\Post($db->getConnection());
$postController = new \controllers\PostController($postModel);
$commentModel = new \models\Comment($db->getConnection());
$commentController = new \controllers\CommentController($commentModel);
$userPosts = $postController->getPostsByUserId($_SESSION['user']['id']);
$userComments = $commentController->getCommentsByUserId($_SESSION['user']['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated = $userController->updateProfile($_SESSION['user']['id'], $_POST['username'], $_POST['email']);
    if ($updated) {
        $_SESSION['user']['username'] = $_POST['username'];
        $_SESSION['user']['email'] = $_POST['email'];
        $success_message = "Perfil actualizado correctamente.";
    } else {
        $error_message = "Error al actualizar el perfil.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
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
                <li><a href="/2DAW/m7blog/app/views/help/contact.php" class="text-white hover:text-gray-300">Contactar</a></li>
            </ul>
        </nav>

        <h1 class="text-4xl font-bold mb-4">Perfil de Usuario</h1>
        <?php if (isset($success_message)): ?>
            <p class="text-green-500 mb-4"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="text-red-500 mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="post" action="" class="bg-gray-800 p-6 rounded-lg shadow-lg mb-6">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-300">Nombre de usuario:</label>
                <input type="text" id="username" name="username" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md" value="<?php echo htmlspecialchars($_SESSION['user']['username']); ?>" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-300">Correo electrónico:</label>
                <input type="email" id="email" name="email" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md" value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>">
            </div>
            <div class="flex justify-end">
                <input type="submit" value="Actualizar perfil" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
            </div>
        </form>

        <?php if ($_SESSION['user']['role'] !== 'subscriber'): ?>
            <h2 class="text-2xl font-bold mb-4">Administrar Mis Posts</h2>
            <input type="text" id="search" placeholder="Buscar post..." class="mb-4 p-2 w-full bg-gray-700 text-white rounded-md">
            <div id="posts" class="grid grid-cols-1 gap-6">
                <?php if (!empty($userPosts)): ?>
                    <?php foreach ($userPosts as $post): ?>
                        <div class="bg-gray-800 p-6 rounded-lg shadow-lg flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($post['title']); ?></h3>
                                <p class="text-gray-400 mb-4"><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 100))); ?>...</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="/2DAW/m7blog/app/views/post/edit.php?post_id=<?php echo $post['id']; ?>" class="text-blue-500 hover:underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13h3l9-9a2.828 2.828 0 00-4-4l-9 9v3z" />
                                    </svg>
                                </a>
                                <a href="/2DAW/m7blog/app/views/post/delete.php?post_id=<?php echo $post['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('¿Está seguro de que desea borrar este post?');">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M9 6v12m6-12v12M4 6l1 14a2 2 0 002 2h10a2 2 0 002-2l1-14M10 6V4a2 2 0 012-2h0a2 2 0 012 2v2" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No has creado ningún post.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <h2 class="text-2xl font-bold mb-4">Historial de Comentarios</h2>
        <div id="comments" class="grid grid-cols-1 gap-6">
            <?php if (!empty($userComments)): ?>
                <?php foreach ($userComments as $comment): ?>
                    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                        <p class="text-gray-400 mb-4"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                        <a href="/2DAW/m7blog/app/views/post/details.php?post_id=<?php echo $comment['post_id']; ?>" class="text-blue-500 hover:underline">Ver post</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No has realizado ningún comentario.</p>
            <?php endif; ?>
        </div>
    </div>
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const posts = document.querySelectorAll('#posts > div');
            posts.forEach(post => {
                const title = post.querySelector('h3').textContent.toLowerCase();
                if (title.includes(searchValue)) {
                    post.style.display = '';
                } else {
                    post.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
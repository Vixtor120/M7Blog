<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/Database.php';
require_once '../../models/Post.php';
require_once '../../controllers/PostController.php';
require_once '../../models/Comment.php';
require_once '../../controllers/CommentController.php';
require_once '../../utils/Auth.php';

use config\Database;
use utils\Auth;

$db = new Database();
$postModel = new \models\Post($db->getConnection());
$postController = new \controllers\PostController($postModel);
$commentModel = new \models\Comment($db->getConnection());
$commentController = new \controllers\CommentController($commentModel);

if (isset($_GET['post_id'])) {
    $post = $postController->getPostById($_GET['post_id']);
    $comments = $commentModel->getCommentsByPostId($_GET['post_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (Auth::isLoggedIn()) {
        $parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;
        $commentController->createComment($_POST['content'], $_GET['post_id'], $_SESSION['user']['id'], $parent_id);
        header("Location: details.php?post_id=" . $_GET['post_id']);
        exit();
    } else {
        header("Location: ../auth/login.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    if (Auth::isLoggedIn()) {
        $commentController->deleteComment($_POST['comment_id'], $_SESSION['user']['id']);
        header("Location: details.php?post_id=" . $_GET['post_id']);
        exit();
    } else {
        header("Location: ../auth/login.php");
        exit();
    }
}

function displayComments($comments, $parent_id = null, $level = 0) {
    foreach ($comments as $comment) {
        if ($comment['parent_id'] == $parent_id) {
            echo '<li class="mb-4 bg-gray-700 p-4 rounded-lg ml-' . ($level * 4) . '">';
            echo '<small class="block text-gray-400">' . htmlspecialchars($comment['username']) . ' el ' . htmlspecialchars($comment['created_at']);
            if ($comment['parent_id']) {
                echo ' respondió a ' . htmlspecialchars($comment['parent_username']);
            }
            echo '</small>';
            echo '<p class="mt-2">' . htmlspecialchars($comment['content']) . '</p>';
            if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $comment['user_id']) {
                echo '<form method="POST" action="" class="inline">';
                echo '<input type="hidden" name="comment_id" value="' . $comment['id'] . '">';
                echo '<button type="submit" name="delete_comment" class="text-red-500 hover:underline">Eliminar</button>';
                echo '</form>';
            }
            echo '<a href="#reply-' . $comment['id'] . '" class="text-blue-500 hover:underline ml-4">Responder</a>';
            echo '<form id="reply-' . $comment['id'] . '" method="POST" action="" class="mt-2 hidden">';
            echo '<input type="hidden" name="parent_id" value="' . $comment['id'] . '">';
            echo '<textarea name="content" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md" required></textarea>';
            echo '<div class="flex justify-end">';
            echo '<button type="submit" name="comment" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Responder</button>';
            echo '</div>';
            echo '</form>';
            echo '<a href="#replies-' . $comment['id'] . '" class="text-blue-500 hover:underline ml-4 toggle-replies">Ver respuestas</a>';
            echo '<ul id="replies-' . $comment['id'] . '" class="hidden">';
            displayComments($comments, $comment['id'], $level + 1);
            echo '</ul>';
            echo '</li>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Post</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href^="#reply-"]').forEach(function(replyLink) {
                replyLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    var formId = this.getAttribute('href').substring(1);
                    var form = document.getElementById(formId);
                    if (form) {
                        form.classList.toggle('hidden');
                    }
                });
            });

            document.querySelectorAll('.toggle-replies').forEach(function(toggleLink) {
                toggleLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    var repliesId = this.getAttribute('href').substring(1);
                    var replies = document.getElementById(repliesId);
                    if (replies) {
                        replies.classList.toggle('hidden');
                        this.textContent = replies.classList.contains('hidden') ? 'Ver respuestas' : 'Ocultar respuestas';
                    }
                });
            });
        });
    </script>
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
                <small class="block text-gray-400">Escrito por: <?php echo htmlspecialchars($post['author']); ?> el <?php echo htmlspecialchars($post['created_at']); ?></small>
            </div>

            <div class="bg-gray-800 p-6 rounded-lg shadow-lg mt-6">
                <h2 class="text-2xl font-bold mb-4">Comentarios</h2>
                <?php if (isset($comments) && !empty($comments)): ?>
                    <ul>
                        <?php displayComments($comments); ?>
                    </ul>
                <?php else: ?>
                    <p>No hay comentarios.</p>
                <?php endif; ?>

                <?php if (Auth::isLoggedIn()): ?>
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-300">Añadir un comentario:</label>
                            <textarea id="content" name="content" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md" required></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" name="comment" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Comentar</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p>Debes <a href="../auth/login.php" class="text-blue-500 hover:underline">iniciar sesión</a> para comentar.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="text-red-500">Post no encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
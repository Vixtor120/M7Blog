<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '\\..\\..\\config\\Database.php';
require_once __DIR__ . '\\..\\..\\models\\Post.php';
require_once __DIR__ . '\\..\\..\\models\\Topic.php';
require_once __DIR__ . '\\..\\..\\controllers\\PostController.php';
require_once __DIR__ . '\\..\\..\\controllers\\TopicController.php';
require_once __DIR__ . '\\..\\..\\utils\\Auth.php';

use config\Database;
use utils\Auth;

if (!Auth::isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit();
}

$db = new Database();
$postModel = new \models\Post($db->getConnection());
$topicModel = new \models\Topic($db->getConnection());
$postController = new \controllers\PostController($postModel);
$topicController = new \controllers\TopicController($topicModel);

// Fetch topics for the dropdown
$query = "SELECT id, name FROM topics";
$stmt = $db->getConnection()->prepare($query);
$stmt->execute();
$topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = basename($_FILES['image']['name']);
        $user_id = $_SESSION['user']['id'];
        $upload_dir = __DIR__ . '/../../public/uploads/' . $user_id . '/';
        
        // Create user-specific directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_path = $upload_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image_url = '2DAW/m7blog/app/public/uploads/' . $user_id . '/' . $image_name;
        }
    }

    $topic_id = isset($_POST['topic_id']) ? $_POST['topic_id'] : null;
    $new_topic_name = isset($_POST['new_topic']) ? $_POST['new_topic'] : null;

    if ($new_topic_name) {
        $topic_id = $topicController->createTopic($new_topic_name);
    }

    if ($topic_id) {
        $postId = $postController->createPost($_POST['title'], $_POST['content'], $_SESSION['user']['id'], $topic_id, $image_url);
        if ($postId) {
            header("Location: details.php?post_id=$postId");
            exit();
        } else {
            $error = "Error al crear el post.";
        }
    } else {
        $error = "Debe seleccionar o crear un tema.";
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
    <style>
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

        <h1 class="text-4xl font-bold mb-4">Crear Nuevo Post de Videojuegos</h1>
        <form method="POST" action="" enctype="multipart/form-data" class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-300">Título:</label>
                <input type="text" id="title" name="title" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-300">Contenido:</label>
                <textarea id="content" name="content" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md" required></textarea>
            </div>
            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-300">Imagen:</label>
                <input type="file" id="image" name="image" class="mt-1 p-2 w-full bg-gray-600 text-white rounded-md">
            </div>
            <div class="mb-4">
                <label for="topic_id" class="block text-sm font-medium text-gray-300">Tema:</label>
                <select id="topic_id" name="topic_id" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md">
                    <option value="">Seleccionar tema existente</option>
                    <?php foreach ($topics as $topic): ?>
                        <option value="<?php echo htmlspecialchars($topic['id']); ?>"><?php echo htmlspecialchars($topic['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="new_topic" class="block text-sm font-medium text-gray-300">O crear nuevo tema:</label>
                <input type="text" id="new_topic" name="new_topic" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md">
            </div>
            <?php if (isset($error)): ?>
                <p class="mt-4 text-center text-red-500"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="flex justify-end">
                <button type="submit" name="action" value="create_post" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Crear Post</button>
            </div>
        </form>
        <div class="mt-4 text-sm text-gray-400">
            <p>Autor: <?php echo htmlspecialchars($_SESSION['user']['username']); ?></p>
            <p>Fecha: <?php echo date('Y-m-d H:i:s'); ?></p>
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
<?php
session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/utils/Auth.php';
require_once __DIR__ . '/models/Post.php';
require_once __DIR__ . '/controllers/PostController.php';
require_once __DIR__ . '/models/Comment.php';
require_once __DIR__ . '/controllers/CommentController.php';

use config\Database;
use models\User;
use controllers\UserController;
use utils\Auth;
use models\Post;
use controllers\PostController;

$db = new Database();
$userModel = new User($db->getConnection());
$userController = new UserController($userModel);
$postModel = new Post($db->getConnection());
$postController = new PostController($postModel);
$commentModel = new \models\Comment($db->getConnection());
$commentController = new \controllers\CommentController($commentModel);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'login') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $query = "SELECT * FROM users WHERE username = :username";
            $stmt = $db->getConnection()->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                header('Location: /2DAW/m7blog/app/views/home/index.php');
                exit();
            } else {
                $error_message = "Credenciales incorrectas.";
                include __DIR__ . '/views/auth/login.php';
                exit();
            }
        } elseif ($_POST['action'] === 'register') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

            // Check if email already exists
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $db->getConnection()->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $error_message = "El correo electrónico ya está registrado.";
                include __DIR__ . '/views/auth/register.php';
                exit();
            }

            $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $db->getConnection()->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);

            if ($stmt->execute()) {
                $success_message = "Usuario registrado correctamente.";
                include __DIR__ . '/views/auth/register.php';
                exit();
            } else {
                $error_message = "Error al registrar el usuario.";
                include __DIR__ . '/views/auth/register.php';
                exit();
            }
        } elseif ($_POST['action'] === 'create_post') {
            $postId = $postController->createPost($_POST['title'], $_POST['content'], $_SESSION['user']['id']);
            if ($postId) {
                header("Location: views/post/details.php?post_id=$postId");
                exit();
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['post_id'])) {
    $post = $postController->getPostById($_GET['post_id']);
    require 'views/post/details.php';
    exit();
}

if (Auth::isLoggedIn()) {
    $userRole = $_SESSION['user']['role'];
    $posts = $postController->getAllPosts();
    require 'views/home/index.php';
    exit();
} else {
    require 'views/auth/login.php';
}
?>
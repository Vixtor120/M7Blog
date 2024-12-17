<?php
session_start();
require 'config/Database.php';
require 'models/User.php';
require 'controllers/UserController.php';
require 'utils/Auth.php';
require 'models/Post.php';
require 'controllers/PostController.php';

use config\Database;
use models\User;
use controllers\UserController;
use utils\Auth;
use models\Post;
use controllers\PostController;

$db = (new Database())->getConnection();
$userModel = new User($db);
$userController = new UserController($userModel);
$postModel = new Post($db);
$postController = new PostController($postModel);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'login') {
            if ($userController->login($_POST['username'], $_POST['password'])) {
                header("Location: views/home/index.php");
                exit();
            } else {
                $error = "Nombre de usuario o contraseña incorrectos.";
            }
        } elseif ($_POST['action'] === 'register') {
            $userController->register($_POST['username'], $_POST['email'], $_POST['password']);
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
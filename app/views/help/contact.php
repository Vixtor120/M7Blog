<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '\\..\\..\\config\\Database.php';
require_once __DIR__ . '\\..\\..\\models\\HelpTicket.php';
require_once __DIR__ . '\\..\\..\\controllers\\HelpTicketController.php'; // Ensure this line is included
require_once __DIR__ . '\\..\\..\\utils\\Auth.php';

use config\Database;
use models\HelpTicket;
use controllers\HelpTicketController; // Ensure this line is included
use utils\Auth;

$db = new Database();
$helpTicketModel = new HelpTicket($db->getConnection());
$helpTicketController = new HelpTicketController($helpTicketModel);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject']) && isset($_POST['message'])) {
    if (Auth::isLoggedIn()) {
        $user_id = $_SESSION['user']['id'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $helpTicketController->createTicket($user_id, $subject, $message);
        $success_message = "Ticket de ayuda enviado correctamente.";
    } else {
        header('Location: ../auth/login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactar con el Administrador</title>
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
                    <li><a href="/2DAW/m7blog/app/views/help/contact.php" class="text-white hover:text-gray-300">Contactar</a></li>
                    <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'writer'): ?>
                        <li><a href="/2DAW/m7blog/app/views/post/create.php" class="text-white hover:text-gray-300">Crear Post</a></li>
                    <?php endif; ?>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <li><a href="/2DAW/m7blog/app/views/admin/panel.php" class="text-white hover:text-gray-300">Panel de Control</a></li>
                        <li><a href="/2DAW/m7blog/app/views/admin/help_tickets.php" class="text-white hover:text-gray-300">Tickets de Ayuda</a></li>
                    <?php endif; ?>
                    <li><a href="/2DAW/m7blog/app/views/auth/logout.php" class="text-white hover:text-gray-300">Cerrar sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <h1 class="text-4xl font-bold mb-4">Contactar con el Administrador</h1>
        <?php if (isset($success_message)): ?>
            <p class="text-green-500 mb-4"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="text-red-500 mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="post" action="" class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <div class="mb-4">
                <label for="subject" class="block text-sm font-medium text-gray-300">Asunto:</label>
                <input type="text" id="subject" name="subject" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="message" class="block text-sm font-medium text-gray-300">Mensaje:</label>
                <textarea id="message" name="message" class="mt-1 p-2 w-full bg-gray-700 text-white rounded-md" required></textarea>
            </div>
            <div class="flex justify-end">
                <input type="submit" value="Enviar" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
            </div>
        </form>
    </div>
    <?php if (isset($_SESSION['user'])): ?>
        <script>
            console.log("Usuario logueado: <?php echo htmlspecialchars($_SESSION['user']['username']); ?>");
            console.log("Rol del usuario: <?php echo htmlspecialchars($_SESSION['user']['role']); ?>");
        </script>
    <?php endif; ?>
</body>
</html>

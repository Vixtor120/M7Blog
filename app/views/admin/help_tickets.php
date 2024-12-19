<?php
require_once __DIR__ . '\\..\\..\\config\\Database.php';
require_once __DIR__ . '\\..\\..\\models\\HelpTicket.php';
require_once __DIR__ . '\\..\\..\\controllers\\HelpTicketController.php';
require_once __DIR__ . '\\..\\..\\utils\\Auth.php';

use config\Database;
use controllers\HelpTicketController; // Ensure this line is included
use models\HelpTicket;
use utils\Auth;

if (!Auth::isLoggedIn() || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$db = new Database();
$helpTicketModel = new HelpTicket($db->getConnection());
$helpTicketController = new HelpTicketController($helpTicketModel);
$tickets = $helpTicketController->getAllTickets();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resolve_ticket'])) {
    $helpTicketController->resolveTicket($_POST['ticket_id']);
    header('Location: help_tickets.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets de Ayuda</title>
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

        <h1 class="text-4xl font-bold mb-4">Tickets de Ayuda</h1>
        <p class="mb-6">Administrar tickets de ayuda enviados por los usuarios.</p>

        <?php if (!empty($tickets)): ?>
            <table class="min-w-full bg-gray-800">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-700">ID</th>
                        <th class="py-2 px-4 border-b border-gray-700">Usuario</th>
                        <th class="py-2 px-4 border-b border-gray-700">Asunto</th>
                        <th class="py-2 px-4 border-b border-gray-700">Mensaje</th>
                        <th class="py-2 px-4 border-b border-gray-700">Estado</th>
                        <th class="py-2 px-4 border-b border-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-700"><?php echo htmlspecialchars($ticket['id']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-700"><?php echo htmlspecialchars($ticket['username']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-700"><?php echo htmlspecialchars($ticket['subject']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-700"><?php echo htmlspecialchars($ticket['message']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-700"><?php echo htmlspecialchars($ticket['status']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-700">
                                <?php if ($ticket['status'] === 'open'): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                        <button type="submit" name="resolve_ticket" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Resolver</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay tickets de ayuda.</p>
        <?php endif; ?>
    </div>
</body>
</html>

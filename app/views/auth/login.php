<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="bg-green-900 flex items-center justify-center h-screen">
    <div class="flex bg-gray-900 w-full max-w-4xl rounded-lg shadow-lg overflow-hidden animate__animated animate__fadeIn">
        <!-- Sección de Inicio de Sesión -->
        <div class="w-1/2 p-8 bg-gray-800 text-white animate__animated animate__fadeInLeft">
            <h2 class="text-2xl font-bold mb-6">Bienvenido - Login</h2>

            <!-- Botón de Inicio con Google -->
            <div class="flex space-x-4 mb-6">
                <button class="flex items-center justify-center w-full p-2 border rounded text-gray-200 border-gray-600 hover:bg-gray-700">
                    <img src="https://img.icons8.com/color/20/000000/google-logo.png" class="mr-2" alt="Google"> Iniciar sesión con Google
                </button>
            </div>

            <!-- Separador -->
            <div class="flex items-center my-4">
                <div class="flex-grow border-t border-gray-600"></div>
                <span class="mx-4 text-gray-400">o</span>
                <div class="flex-grow border-t border-gray-600"></div>
            </div>

            <!-- Formulario -->
            <form method="POST" action="/2DAW/m7blog/app/index.php">
                <input type="hidden" name="action" value="login">

                <label for="username" class="block mb-2 text-sm font-medium">Usuario</label>
                <input type="text" id="username" name="username" required
                       class="w-full p-2 mb-4 rounded bg-gray-700 text-white border border-gray-600">

                <label for="password" class="block mb-2 text-sm font-medium">Contraseña</label>
                <input type="password" id="password" name="password" required
                       class="w-full p-2 mb-4 rounded bg-gray-700 text-white border border-gray-600">

                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white p-2 rounded">
                    Iniciar sesión
                </button>
            </form>

            <p class="mt-4 text-center text-gray-400">
                ¿No tienes cuenta? <a href="/2DAW/m7blog/app/views/auth/register.php" class="text-blue-500 hover:underline">Regístrate aquí</a>
            </p>
        </div>

        <!-- Sección Informativa -->
        <div class="w-1/2 bg-green-500 text-white flex flex-col justify-center items-center p-8 animate__animated animate__fadeInRight">
            <div class="mb-6">
                <img src="/2DAW/m7blog/app/public/img/logo.png" alt="Logo" class="h-40">
            </div>
            <h1 class="text-3xl font-bold mb-4 text-center leading-tight">
                ¡Explora el mejor Blog de Videojuegos! 
            </h1>
            <p class="text-center text-sm mb-6">
                Únete a nuestra comunidad de gamers y mantente al día con las últimas noticias, reseñas y guías de tus juegos favoritos.
            </p>
        </div>
    </div>
</body>
</html>

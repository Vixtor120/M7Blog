<?php
namespace controllers;

use models\User;
use utils\Auth;

class UserController {
    private $userModel;

    public function __construct(User $user) {
        $this->userModel = $user;
    }

    public function register($username, $email, $password) {
        try {
            $this->userModel->create($username, $email, $password);
            echo "Usuario registrado correctamente.";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function login($username, $password) {
        try {
            $user = $this->userModel->login($username, $password);
            Auth::login($user);
            echo "<script>console.log('Inicio de sesión exitoso. Bienvenido, " . $user['username'] . "');</script>";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function verifyCredentials($username, $password) {
        $user = $this->userModel->getUserByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }

    public function getAllUsers() {
        return $this->userModel->getAll();
    }

    public function updateProfile($userId, $username, $email) {
        return $this->userModel->update($userId, $username, $email);
    }

    public function updateUserRole($user_id, $role) {
        return $this->userModel->updateRole($user_id, $role);
    }

    public function deleteUser($userId) {
        return $this->userModel->delete($userId);
    }

    public function getUserById($id) {
        return $this->userModel->findById($id);
    }
}
?>
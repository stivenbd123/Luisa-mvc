<?php
require_once __DIR__ . '/../config/database.php';

class AuthController {
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role']; 

                header("Location: index.php?action=home");
                exit();
            } else {
                echo "<script>alert('Correo o contraseña incorrectos'); window.location.href='index.php?action=login';</script>";
            }
        }
    }
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            // Forzamos que todo el que se registre por fuera sea recepcionista
            $role = 'recepcionista'; 

            $db = new Database();
            $conn = $db->getConnection();

            // 1. Validar que el correo no exista
            $stmtCheck = $conn->prepare("SELECT id FROM users WHERE email = :email");
            $stmtCheck->bindParam(':email', $email);
            $stmtCheck->execute();
            
            if ($stmtCheck->fetch()) {
                $_SESSION['error'] = 'Este correo ya está registrado. Inicia sesión.';
                header("Location: index.php?action=register");
                exit();
            }

            // 2. Guardar el nuevo usuario
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword, $role]);

            // 3. Autologuear al usuario recién creado
            $_SESSION['user_id'] = $conn->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['role'] = $role;

            // Redirigir al home
            header("Location: index.php?action=home");
            exit();
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}
?>
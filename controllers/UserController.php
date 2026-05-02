<?php
require_once __DIR__ . '/../config/database.php';

class UserController
{
    private function checkAdminAccess()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
                    <h2 style='color:#ef4444;'>Acceso Denegado ✋</h2>
                    <p>Solo los administradores pueden gestionar usuarios.</p>
                    <a href='index.php?action=home'>Volver al inicio</a>
                 </div>");
        }
    }

    public function index()
    {
        $this->checkAdminAccess();
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->query("SELECT * FROM users ORDER BY name ASC");
        $users = $stmt->fetchAll();

        // RUTA CORREGIDA: Apuntando a tu nueva estructura
        require_once 'views/users/index.php';
    }

    public function create()
    {
        $this->checkAdminAccess();
        require_once 'views/users/create.php';
    }

    public function edit()
    {
        $this->checkAdminAccess();
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();

            require_once 'views/users/edit.php';
        }
    }

    public function store()
    {
        $this->checkAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role'];

            $db = new Database();
            $conn = $db->getConnection();

            // Validar unique email
            $stmtCheck = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmtCheck->execute([$email]);
            if ($stmtCheck->fetch()) {
                $_SESSION['error'] = 'Este correo ya está registrado por otro usuario.';
                header("Location: index.php?action=usuarios");
                exit();
            }

            // Hashear contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword, $role]);

            $_SESSION['success'] = 'Usuario creado correctamente.';
            header("Location: index.php?action=usuarios");
            exit();
        }
    }

    public function update()
    {
        $this->checkAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $role = $_POST['role'];
            $password = $_POST['password'];

            $db = new Database();
            $conn = $db->getConnection();

            // Validar que el correo no lo tenga otro
            $stmtCheck = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmtCheck->execute([$email, $id]);
            if ($stmtCheck->fetch()) {
                $_SESSION['error'] = 'El correo ingresado ya pertenece a otro usuario.';
                header("Location: index.php?action=usuarios");
                exit();
            }

            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?");
                $stmt->execute([$name, $email, $role, $hashedPassword, $id]);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
                $stmt->execute([$name, $email, $role, $id]);
            }

            $_SESSION['success'] = 'Datos de usuario actualizados.';
            header("Location: index.php?action=usuarios");
            exit();
        }
    }

    public function destroy()
    {
        $this->checkAdminAccess();

        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            if ($_SESSION['user_id'] == $id) {
                $_SESSION['error'] = 'No puedes eliminar tu propia cuenta de administrador.';
                header("Location: index.php?action=usuarios");
                exit();
            }

            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = 'Usuario eliminado del sistema.';
            header("Location: index.php?action=usuarios");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión | MediSys</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); }
        .login-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); width: 100%; max-width: 400px; }
        .login-container h1 { text-align: center; color: #1e1b4b; margin-bottom: 25px; font-size: 24px; }
        .alert-error { background: #fee2e2; color: #ef4444; padding: 10px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #475569; font-weight: 500; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; transition: border 0.3s; }
        .form-group input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .login-btn { width: 100%; background: #4f46e5; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.3s; font-size: 16px; }
        .login-btn:hover { background: #4338ca; }
        .footer-text { text-align: center; margin-top: 20px; color: #64748b; font-size: 14px; }
        .footer-text a { color: #4f46e5; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="login-container">
    <h1>MediSys Login</h1>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert-error">
            <?= $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); // Borramos el error tras mostrarlo ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=procesar_login">
        <div class="form-group">
            <label>Correo Electrónico</label>
            <input type="email" name="email" required placeholder="admin@consultorio.com">
        </div>

        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>

        <button type="submit" class="login-btn">Ingresar al Sistema</button>
    </form>
    <div class="footer-text" style="text-align: center; margin-top: 20px; color: #64748b; font-size: 14px;">
        ¿No tienes cuenta?
        <a href="index.php?action=register" style="color: #4f46e5; text-decoration: none; font-weight: 600;">Regístrate aquí</a>
    </div>
</div>

</body>
</html>
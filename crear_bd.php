<?php
// crear_bd.php
$db_file = __DIR__ . '/medisys.sqlite';

try {
    $conn = new PDO("sqlite:" . $db_file);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Crear las tablas (Estructura para SQLite)
    $queries = [
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'recepcionista'
        )",
        "CREATE TABLE IF NOT EXISTS patients (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            document TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT NOT NULL
        )",
        "CREATE TABLE IF NOT EXISTS specialties (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL
        )",
        "CREATE TABLE IF NOT EXISTS doctors (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            specialty_id INTEGER NOT NULL,
            FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS consulting_rooms (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            specialty_id INTEGER NOT NULL,
            FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE CASCADE
        )",
        "CREATE TABLE IF NOT EXISTS appointments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            patient_id INTEGER NOT NULL,
            doctor_id INTEGER NOT NULL,
            consulting_room_id INTEGER NOT NULL,
            appointment_date DATETIME NOT NULL,
            status TEXT DEFAULT 'Programada',
            FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
            FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
            FOREIGN KEY (consulting_room_id) REFERENCES consulting_rooms(id) ON DELETE CASCADE
        )"
    ];

    foreach ($queries as $query) {
        $conn->exec($query);
    }

    // 2. Insertar al usuario Administrador si no existe
    $checkAdmin = $conn->query("SELECT id FROM users WHERE email = 'admin@consultorio.com'")->fetch();
    if (!$checkAdmin) {
        $password = password_hash('12345678', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Super Administrador', 'admin@consultorio.com', $password, 'admin']);
        echo "<p>✅ Usuario Administrador creado con éxito.</p>";
    }

    echo "<h1>🚀 Base de datos SQLite inicializada.</h1>";
    echo "<p>El archivo <strong>medisys.sqlite</strong> ha sido creado en tu proyecto.</p>";
    echo "<a href='index.php?action=login' style='padding: 10px; background: #0284c7; color: white; text-decoration: none; border-radius: 5px;'>Ir al Login</a>";

} catch (Exception $e) {
    echo "Error crítico: " . $e->getMessage();
}
?>
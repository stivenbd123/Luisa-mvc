<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MediSys')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; background-color: #f1f5f9; min-height: 100vh; }
        .sidebar { width: 250px; background-color: #0f172a; color: white; position: fixed; height: 100%; z-index: 100; }
        .sidebar-header { padding: 20px; background-color: #0284c7; text-align: center; }
        .sidebar-header h2 { font-weight: 700; letter-spacing: 1px; color: #ffffff; }
        .sidebar-menu { list-style: none; padding: 20px 0; }
        .sidebar-menu li a { display: block; color: #cbd5e1; text-decoration: none; padding: 15px 25px; font-size: 15px; border-left: 3px solid transparent; transition: all 0.2s; }
        .sidebar-menu li a:hover { background-color: #1e293b; color: #ffffff; border-left: 3px solid #0ea5e9; }
        .main-content { flex: 1; margin-left: 250px; display: flex; flex-direction: column; width: calc(100% - 250px); }
        .topbar { background-color: #ffffff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); z-index: 10; }
        .user-info { color: #334155; font-size: 15px; }
        .logout-btn { background-color: #ef4444; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: background 0.2s; }
        .logout-btn:hover { background-color: #dc2626; }
        .content-wrapper { padding: 30px; overflow-y: auto; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>MediSys</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="{{ route('patients.index') }}">Pacientes</a></li>
            <li><a href="{{ route('specialties.index') }}">Especialidades</a></li>
            <li><a href="{{ route('doctors.index') }}">Cuerpo Médico</a></li>
            <li><a href="{{ route('consulting_rooms.index') }}">Consultorios</a></li>
            <li><a href="{{ route('appointments.index') }}">Citas</a></li>
            <li><a href="{{ route('reports.index') }}">Historial Clínico</a></li>
            <li><a href="{{ route('exports.view') }}">Exportar Reportes</a></li>
            
            {{-- Botón para el administrador --}}
            @if(auth()->check() && auth()->user()->role === 'admin')
                <li style="border-top: 1px solid #1e293b; margin-top: 10px;">
                    <a href="{{ route('users.index') }}" style="color: #fbbf24; font-weight: 600;">Gestionar Usuarios</a>
                </li>
            @endif
        </ul>
    </nav>
    <main class="main-content">
        <header class="topbar">
            <div class="user-info">
                Usuario: <strong>{{ auth()->user()->name ?? 'Staff' }}</strong> 
                <span style="font-size: 12px; color: #64748b; margin-left: 10px;">({{ strtoupper(auth()->user()->role ?? '') }})</span>
            </div>
            <form action="{{ route('logout') }}" method="POST"> 
                @csrf 
                <button type="submit" class="logout-btn">Cerrar Sesión</button>
            </form>
        </header>
        <div class="content-wrapper"> 
            @yield('content') 
        </div>
    </main>
</body>
</html>
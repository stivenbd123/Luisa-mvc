<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente Clínico - {{ $patient->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .patient-info { background: #f8fafc; padding: 15px; border: 1px solid #ddd; margin-bottom: 25px; }
        .appt-box { margin-bottom: 20px; padding: 10px; border-bottom: 1px solid #eee; }
        .appt-date { font-weight: bold; font-size: 14px; color: #0284c7; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>SISTEMA MÉDICO MEDISYS</h1>
        <h2>Expediente Clínico Individual</h2>
    </div>

    <div class="patient-info">
        <strong>Nombre:</strong> {{ $patient->name }}<br>
        <strong>Documento:</strong> {{ $patient->document }}<br>
        <strong>Fecha de Emisión:</strong> {{ date('d/m/Y H:i') }}
    </div>

    <h3>Historial de Atenciones</h3>
    @foreach($appointments as $appt)
    <div class="appt-box">
        <div class="appt-date">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d/m/Y') }}</div>
        <strong>Médico:</strong> Dr. {{ $appt->doctor->name }} ({{ $appt->doctor->specialty->name }})<br>
        <strong>Consultorio:</strong> {{ $appt->consultingRoom->name }}<br>
        <strong>Estado:</strong> {{ $appt->status }}<br>
        <strong>Observaciones:</strong><br>
        <p>{{ $appt->notes ?? 'N/A' }}</p>
    </div>
    @endforeach
</body>
</html>
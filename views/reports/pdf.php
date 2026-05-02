<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Citas - MediSys</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; text-transform: uppercase; font-size: 11px; }
        
        .footer { margin-top: 30px; font-size: 10px; text-align: center; color: #777; }
        
        /* Ocultar botones en la impresión final */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Imprimir / Guardar PDF</button>
    </div>

    <div class="header">
        <h1>MediSys - Sistema Clínico</h1>
        <p>Reporte General de Citas Médicas</p>
        <p>Fecha de generación: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha y Hora</th>
                <th>Paciente</th>
                <th>Documento</th>
                <th>Médico Asignado</th>
                <th>Consultorio</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appt)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d/m/Y H:i') }}</td>
                    <td>{{ $appt->patient->name }}</td>
                    <td>{{ $appt->patient->document }}</td>
                    <td>Dr. {{ $appt->doctor->name }}</td>
                    <td>{{ $appt->consultingRoom->name }}</td>
                    <td>{{ strtoupper($appt->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No hay citas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el sistema MediSys.
    </div>

</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="7" style="text-align: center; font-size: 18px; font-weight: bold; height: 40px; background-color: #ffffff;">
                    Reporte General de Citas - MediSys
                </th>
            </tr>
            <tr>
                <th style="background-color: #0f172a; color: #ffffff; font-weight: bold; border: 1px solid #000000; width: 20px;">Fecha y Hora</th>
                <th style="background-color: #0f172a; color: #ffffff; font-weight: bold; border: 1px solid #000000; width: 30px;">Paciente</th>
                <th style="background-color: #0f172a; color: #ffffff; font-weight: bold; border: 1px solid #000000; width: 20px;">Documento</th>
                <th style="background-color: #0f172a; color: #ffffff; font-weight: bold; border: 1px solid #000000; width: 30px;">Médico Asignado</th>
                <th style="background-color: #0f172a; color: #ffffff; font-weight: bold; border: 1px solid #000000; width: 25px;">Especialidad</th>
                <th style="background-color: #0f172a; color: #ffffff; font-weight: bold; border: 1px solid #000000; width: 20px;">Consultorio</th>
                <th style="background-color: #0f172a; color: #ffffff; font-weight: bold; border: 1px solid #000000; width: 15px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appt)
                <tr>
                    <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d/m/Y H:i') }}</td>
                    <td style="border: 1px solid #000000;">{{ $appt->patient->name }}</td>
                    <td style="border: 1px solid #000000;">{{ $appt->patient->document }}</td>
                    <td style="border: 1px solid #000000;">Dr. {{ $appt->doctor->name }}</td>
                    <td style="border: 1px solid #000000;">{{ $appt->doctor->specialty->name ?? 'N/A' }}</td>
                    <td style="border: 1px solid #000000;">{{ $appt->consultingRoom->name }}</td>
                    <td style="border: 1px solid #000000;">{{ strtoupper($appt->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
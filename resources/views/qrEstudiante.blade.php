<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credencial de Estudiante</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            font-size: 22px;
        }

        .content {
            margin-top: 20px;
        }

        .content h1 {
            text-align: center;
            font-size: 18px;
        }

        .content table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .content table td {
            padding: 5px 10px;
            vertical-align: top;
        }

        .qr-section {
            text-align: center;
            margin-top: 30px;
        }

        .qr-section img {
            margin: 0 auto;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Credencial de Estudiante</h2>
    </div>

    <div class="content">
        <table>
            <tr>
                <td><strong>Nombre:</strong></td>
                <td>{{ $data['nombre'] }}</td>
            </tr>
            <tr>
                <td><strong>Unidad Administrativa:</strong></td>
                <td>{{ $data['unidadAdministrativa'] }}</td>
            </tr>
            <tr>
                <td><strong>Programa:</strong></td>
                <td>{{ $data['programa'] }}</td>
            </tr>
            <tr>
                <td><strong>Fecha de Inicio:</strong></td>
                <td>{{ $data['fechaInicio'] }}</td>
            </tr>
            <tr>
                <td><strong>Fecha de Fin:</strong></td>
                <td>{{ $data['fechaFin'] }}</td>
            </tr>
            <tr>
                <td><strong>Horario:</strong></td>
                <td>{{ $data['horario'] }}</td>
            </tr>
        </table>
    </div>

    <div class="qr-section">
        <img src="data:image/png;base64, {!! base64_encode(QrCode::size(200)->generate($data['qr'])) !!}" alt="QR Code">
    </div>

    <div class="footer">
        Este QR es válido únicamente para el período y horario especificados.
    </div>
</body>

</html>
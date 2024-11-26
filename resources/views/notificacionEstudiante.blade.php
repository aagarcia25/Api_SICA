<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de QR para Acceso</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .content {
            margin: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>¡Hola, {{ $data->Nombre }}!</h1>
        <p>
            Te adjuntamos un formato digital en PDF que incluye tu código QR para identificarte. Este formato no sustituye la credencial física oficial.
        </p>
    </div>


    <div class="content">
        <p><strong>Unidad Administrativa:</strong> {{ $data->UnidadAdministrativa }}</p>
        <p><strong>Programa:</strong> {{ $data->TipoEstudiante }}</p>
        <p><strong>Vigencia:</strong> Desde {{ $data->FechaInicio }} hasta {{ $data->FechaFin }}</p>
        <p><strong>Teléfono de contacto:</strong> {{ $data->Telefono }}</p>
    </div>

    <div class="footer">
        Este correo es generado automáticamente. Por favor, no respondas.
    </div>
</body>

</html>
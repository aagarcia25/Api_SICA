{{-- resources/views/pdf/visita.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificación de visita</title>
    <style>
        *          { font-family: Arial, sans-serif; }
        body       { margin: 0; padding: 0; }
        h1         { text-align: center; margin: 0 0 20px; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        table.meta td { padding: 4px 6px; vertical-align: top; }
        .label     { width: 130px; font-weight: bold; }
        .qr-section{ text-align: center; margin-top: 40px; width: 100%; }
        .qr-section img { margin: 0 auto; }
        .observ    { margin-top: 30px; }
        .centered-img { text-align: center; margin-top: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('img/TesoreriaLogo.png');
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoSrc = 'data:image/png;base64,' . $logoData;
    @endphp
    
    <div class="centered-img">
        <img src="{{ $logoSrc }}" style="width: 230px;">
    </div>

    <h1>Notificación de visita</h1>

    <table class="meta">
        <tr><td class="label">Visitante:</td><td>{{ $data['visitante'] }}</td></tr>
        <tr><td class="label">Edificio:</td><td>{{ $data['edificio'] }}</td></tr>
        <tr><td class="label">Fecha:</td><td>{{ $data['FechaVisita'] }}</td></tr>
        <tr><td class="label">Duración:</td><td>{{ $data['Duracion'] }}</td></tr>
        <tr><td class="label">Dirección:</td><td>{{ $data['Direccion'] }}</td></tr>
        <tr><td class="label">Persona a visitar:</td><td>{{ $data['receptor'] }}</td></tr>
        <tr><td class="label">Dependencia:</td><td>{{ $data['entidadreceptor'] }}</td></tr>
        <tr><td class="label">Piso:</td><td>{{ $data['pisoreceptorrr'] }}</td></tr>
    </table>

    @if(!empty($data['observaciones']))
        <div class="observ">
            <strong>Observaciones:</strong><br>
            {{ $data['observaciones'] }}
        </div>
    @endif

    {{-- QR generado on‑the‑fly con la librería simple‑qrcode --}}
    <div class="qr-section">
        <img src="data:image/png;base64,{!! base64_encode(
                QrCode::size(400)->generate($data['id'])
        ) !!}" alt="QR Code">
    </div>

</body>
</html>

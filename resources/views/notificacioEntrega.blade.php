<h1>
    Notificación de visita agendada
</h1>
<p>
<h3> Visitante: {{ $data->visitante }}</h3>
</p>
<br />
<p>
<h3><b>{{ $data->edificio }}</h3>
</p>
<ol>
    <li>Fecha: {{ $data->FechaVisita }}</li>
    <li>Duración: {{ $data->Duracion }}</li>
    <li>Dirección: {{ $data->Direccion }}</li>
    <li>Persona a visitar: {{ $data->receptor }}</li>
    <li>Dependencia:{{ $data->entidadreceptor }}</li>
    <li>{{ $data->pisoreceptorrr }} </li>
    <li>Observaciones:{{ $data->observaciones }}</li>
</ol>


<div align="center" class="visible-print text-center">
    {!! QrCode::size(100)->generate($data->id) !!}
    <p>Muestra el código al acudir a tu visita para identificarte fácilmente.</p>
</div>


<br /><br /><br />
<div align="center"> Este correo es generado automaticamente *No Responder*</div>
<br />
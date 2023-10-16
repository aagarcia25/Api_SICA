
<h1>
Notificación de visita agendada
</h1>
<p><h3> Hola: {{$data->visitante}}</h3></p>
<br />
<br />
<p><h3> A continuación, vienen los detalles de tu visita a <b>{{$data->edificio}}</h3></p>
 <ol>
  <li>Fecha: {{$data->FechaVisita}}</li>
  <li>Duración:  {{$data->Duracion}}</li>
  <li>Dirección: {{$data->Direccion}}</li>
  <li>Persona a visitar: {{$data->receptor}}</li>
  <li>{{$data->entidadreceptor }}</li>
  <li>{{ $data->pisoreceptorrr }} </li>
</ol>

<p>Ruta del código QR: {{ $rutaTemporalqr }}</p>
<p>Ruta del código QR: {{asset($rutaTemporalqr) }}</p>
   <!-- Verifica si la variable está definida antes de usarla -->
@if (isset($rutaTemporalqr))
    <img src="{{ asset($rutaTemporalqr)  }}" alt="Código QR" type="image/png">
@else
    <!-- Puedes mostrar un mensaje o un marcador de posición en caso de que la variable no esté definida -->
    <p>La imagen QR no está disponible</p>
@endif


    <br /><br /><br />
    Este correo es generado automaticamente  *No Responder*
    <br />

    <br /><br /><br /><br /><br />

<div align="center">Atentamente</div>
<div align="center">Sistema de Control de Accesos</div>

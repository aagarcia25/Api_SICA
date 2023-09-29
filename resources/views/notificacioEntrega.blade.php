
    <h1>
      Notificación de visita agendada
    </h1>

   <p><h3> Hola: Adolfo Angel Garcia Martinez</h3></p>
 <br /><br />
<p><h3> A continuación, vienen los detalles de tu visita a <b>SECRETARIA DE FINANZAS Y TESORERIA GENERAL DEL ESTADO DE NUEVO LEÓN</h3></p>
 <ol>
  <li>Fecha: {{$data->FechaVisita}}</li>
  <li>Duración:  {{$data->Duracion}}</li>
  <li>Dirección: {{$data->Duracion}}</li>
  <li>Persona a visitar: {{$data->receptor}}</li>
  <li>{{$data->entidadreceptor}}</li>
  <li>{{ $data->pisoreceptorrr }} </li>

</ol>

<div class="visible-print text-center">
    {!! QrCode::size(100)->generate("1d6828d2-e6f8-42e1-b610-cebfa2a45cb3"); !!}
    <p>Muestra el código al acudir a tu visita para identificarte fácilmente.</p>
</div>

    <br /><br /><br />
    Este correo es generado automaticamente  *No Responder*
    <br />

    <br /><br /><br /><br /><br />

    <div align="center">Atentamente</div>
    <div align="center">Sistema de Control de Accesos</div>

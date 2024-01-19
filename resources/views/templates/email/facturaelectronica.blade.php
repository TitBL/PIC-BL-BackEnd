@component('mail::message')
<br><strong>{{$cliente}}</strong><br>
<br>Has recibido un nuevo documento electrónico de<br>
<img src="{{$logoEmpresa}}" height="Auto" width="20%"><br>
{{$nombreComercial}}<br>
{{$razonSocial}}<br>

@if($valor!=0)
@component('mail::panel')
Por el valor de:
<br>

<p class="money"><strong>{{'$' . $valor}}</strong></p>
@endcomponent
@endif

<br><strong>{{$documentoLeyenda}}</strong>
<br>{{$numeroDocumento}}<br>
<br>
<strong>CLAVE DE ACCESO/AUTORIZACION</strong>
<br>{{$claveAcceso}}<br>
<br>
<strong>FECHA Y HORA DE AUTORIZACION</strong>
<br> {{$fechaAutorizo}}<br>


@endcomponent
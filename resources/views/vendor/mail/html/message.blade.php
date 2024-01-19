@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.enterprise_url')])
<!-- {{ config('app.name') }} -->
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
 
<img src="https://azulser.com/wp-content/uploads/2020/05/Fondo-HABNIS-blanco-transparente.png" height="Auto"
    width="18%"><br>
Powered by {{ config('app.name') }} © {{ date('Y') }}. @lang('All rights reserved.')
<br>
<a href="{{ config('app.enterprise_url') }}" target="_blank"
    rel="noopener"><strong>{{ config('app.enterprise_web') }}</strong></a><br><br>
    <small   style="color:smoke;" ><em>Si tiene alguna consulta con respecto a la información brindada no dude en comunicarse a <a href="mailto:soporte@azulser.com">soporte@azulser.com</a>, caso contrario no es necesario responder a este correo electrónico.</em></small>
@endcomponent
@endslot


































@endcomponent
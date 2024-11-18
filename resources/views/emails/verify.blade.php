@component('mail::message')
# Email Verification

Obrigado por se cadastrar.
Seu código de 6 dígitos é: {{$pin}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent

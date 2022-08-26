@component('mail::message')
<h1>Hello!</h1>
Your token is {{ $token }}. <br />
Please click the button below to reset your password.
@component('mail::button', ['url' => 'https://login-signup-system.vercel.app/password/reset'])
Reset Password
@endcomponent
@endcomponent
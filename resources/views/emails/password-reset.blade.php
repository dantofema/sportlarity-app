<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <link rel="preconnect"
          href="https://fonts.googleapis.com">
    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap"
          rel="stylesheet">
    <title>Password Reset - Sportlarity</title>
</head>
<body style="margin: 0">
<div style="display: flex; align-items: center; justify-content: center; flex-direction: column; margin-top: 1.25rem; font-family: Nunito, sans-serif">
    <section style="max-width: 42rem; background-color: #fff;">
        <header style="padding-top: 1rem; padding-bottom: 1rem; display: flex; justify-content: center; width: 100%; background-color: #292340">
            <a href="{{ config('app.url') }}">
                <img src="{{ asset('images/logo.png') }}"
                     style="height: 75px"
                     alt="sportlarity"/>
            </a>
        </header>

        <div style="width: 100%; height: 2px; background-color: #365CCE;"></div>

        <div style="text-align: center; width: 100%; margin-top: 15px;">
            <div style="font-weight: bold; font-size: 25px;">
                Tu password ha sido <span style="position: relative">reseteado
                <div style="position: absolute; height: 3px; background-color: #365CCE; width: 55px; left: 1px; bottom: -4px;"></div>
            </span>
            </div>
        </div>

        <main style="text-align: start; padding-left: 20px; padding-right: 20px;">
            <p>Hola {{ $user->name }},</p>

            <p>Un administrador ha reseteado tu password. Tus nuevas credenciales son:</p>

            <ul>
                <li>Usuario: {{ $user->email }}</li>
                <li>Password: {{ $temporaryPassword }}</li>
            </ul>

            <p>
                <span style="font-weight: bold;">Importante: Debes cambiar tu password la primera vez que inicies sesion.</span>
            </p>

            <p>Si no solicitaste este cambio, contacta a un administrador inmediatamente.</p>

            <a href="{{ config('app.url') }}">
                <button style="padding: 0.5rem 1.25rem; margin-top: 1.5rem; font-size: 14px; font-weight: bold; text-transform: capitalize; background-color: #182D4B; color: #fff; transition-property: background-color; transition-duration: 300ms; transform: none; border-radius: 0.375rem; border: 1px none; outline: none; cursor: pointer;">
                    Iniciar sesion
                </button>
            </a>

            <p>
                Gracias, <br/>
                Sportlarity Team
            </p>
        </main>
    </section>
</div>
</body>
</html>

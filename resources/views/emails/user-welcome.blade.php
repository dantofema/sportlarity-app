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
    <title>Welcome to Sportlarity</title>
    <style>
        .footertext {
            font-size: 12px;
        }

        @media (min-width: 640px) {
            .footertext {
                font-size: 16px;
            }
        }
    </style>

</head>
<body style="margin : 0">
<div style="display: flex; align-items: center; justify-content: center; flex-direction: column; margin-top: 1.25rem; font-family : Nunito, sans-serif ">
    <section style="max-width: 42rem; background-color: #fff;">
        <header style="padding-top: 1rem; padding-bottom: 1rem; display:
        flex; justify-content: center; width: 100%; background-color: #292340">
            <a href="{{ config('app.url') }}">
                <img src="{{ asset('images/logo.png') }}"
                     style="height: 75px"
                     alt="sportlarity"/>
            </a>
        </header>

        <div style="width : 100%; height : 2px; background-color : #365CCE;"></div>

        <div style="text-align : center; width : 100%; margin-top : 15px; ">
            <div style="font-weight : bold; font-size : 25px;">
                Gracias por <span style="position : relative"> registrarte!
            <div style="position :absolute; height : 3px; background-color : #365CCE; width : 55px; left : 1px; bottom : -4px;"></div>
          </span>
            </div>
        </div>

        <main style="text-align : start; padding-left : 20px; padding-right : 20px;">
            <p>Muchas gracias por registrarte y comenzar a usar nuestra
                aplicación. Esto nos ayudará a lograr una mirada más sistemática
                sobre los hábitos claves para el logro del bienestar.</p>
            <p>La app tiene 4 grandes secciones:</p>
            <ul>
                <li>Dashboard: donde podrás consultar el diario cargado el día
                    anterior y algunos datos agregados.
                </li>
                <li>Diaries: donde registrarás respuestas diarias a consultas
                    concretas a partir de tu percepción sobre hábitos
                    claves.
                </li>
                <li>Notes: donde iremos compartiendo contenido a cargo de
                    nuestros
                    expertos, focalizadas en el objetivo que hayas fijado para
                    tu
                    proceso. Nuestro compromiso es brindarte información con
                    bases
                    científicas y pertinente sobre cómo avanzar en el logro de
                    tus
                    metas. También en esta sección encontrarás los resultados de
                    tus
                    test iniciales y el feedback que te iremos brindando sobre
                    tu
                    avance.
                </li>
                <li>Plans: donde podrás consultar - cada vez que lo desees - tu
                    plan de trabajo tanto a nivel físico (con videos
                    explicativos)
                    como nutricional.
                </li>
            </ul>

            <p>
                Tus credenciales para acceder son:
            </p>

            <ul>
                <li>Usuario: {{ $user->email }}</li>
                <li>Password: sportlarity</li>
            </ul>
            <p>
                <span style="font-weight : bold;">No olvides cambiar tu password</span>
            </p>

            <a href="{{ config('app.url') }}">

                <button style="padding: 0.5rem 1.25rem;margin-top: 1.5rem; font-size: 14px; font-weight: bold; text-transform: capitalize; background-color: #182D4B; color: #fff; transition-property: background-color; transition-duration: 300ms; transform: none; border-radius: 0.375rem; border: 1px none;outline: none; cursor: pointer;">
                    Visitar sitio
                </button>
            </a>

            <p>
                Gracias, <br/>
                Sportlarity Team
            </p>
        </main>

        {{--        <footer style="margin-top: 2rem;">--}}
        {{--            <div style="background-color: rgba(209, 213, 219, 0.6); height: 200px; display: flex; flex-direction: column; gap: 1.25rem; justify-content: center; align-items: center;">--}}
        {{--                <div style="text-align: center; display: flex; flex-direction: column; gap: 0.75rem;">--}}
        {{--                    <h1 style="color: #182D4B; font-weight: bold;  font-size: 20px; letter-spacing : 2px;">--}}
        {{--                        Ponerse en contacto--}}
        {{--                    </h1>--}}
        {{--                    --}}{{--                    <a--}}
        {{--                    --}}{{--                            href="tel:+54-11-123-5678"--}}
        {{--                    --}}{{--                            style="color: #4b5563;"--}}
        {{--                    --}}{{--                            alt="+54-11-123-5678"--}}
        {{--                    --}}{{--                    >--}}
        {{--                    --}}{{--                        +54-11-123-5678--}}
        {{--                    --}}{{--                    </a>--}}

        {{--                    <a--}}
        {{--                            href="mailto:info@sportlarity.com"--}}
        {{--                            style="color: #4b5563;"--}}
        {{--                            alt="info@sportlarity.com"--}}
        {{--                    >--}}
        {{--                        info@sportlarity.com--}}
        {{--                    </a>--}}
        {{--                </div>--}}
        {{--                <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem;">--}}
        {{--                    --}}{{--                    <a href="#">--}}
        {{--                    --}}{{--                        <svg--}}
        {{--                    --}}{{--                                stroke="currentColor"--}}
        {{--                    --}}{{--                                fill="gray"--}}
        {{--                    --}}{{--                                stroke-width="0"--}}
        {{--                    --}}{{--                                viewBox="0 0 16 16"--}}
        {{--                    --}}{{--                                height="18"--}}
        {{--                    --}}{{--                                width="18"--}}
        {{--                    --}}{{--                                xmlns="http://www.w3.org/2000/svg"--}}
        {{--                    --}}{{--                        >--}}
        {{--                    --}}{{--                            <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"></path>--}}
        {{--                    --}}{{--                        </svg>--}}
        {{--                    --}}{{--                    </a>--}}

        {{--                    --}}{{--                    <a href="#">--}}
        {{--                    --}}{{--                        <svg--}}
        {{--                    --}}{{--                                stroke="currentColor"--}}
        {{--                    --}}{{--                                fill="gray"--}}
        {{--                    --}}{{--                                stroke-width="0"--}}
        {{--                    --}}{{--                                viewBox="0 0 1024 1024"--}}
        {{--                    --}}{{--                                height="18"--}}
        {{--                    --}}{{--                                width="18"--}}
        {{--                    --}}{{--                                xmlns="http://www.w3.org/2000/svg"--}}
        {{--                    --}}{{--                        >--}}
        {{--                    --}}{{--                            <path d="M512 378.7c-73.4 0-133.3 59.9-133.3 133.3S438.6 645.3 512 645.3 645.3 585.4 645.3 512 585.4 378.7 512 378.7zM911.8 512c0-55.2.5-109.9-2.6-165-3.1-64-17.7-120.8-64.5-167.6-46.9-46.9-103.6-61.4-167.6-64.5-55.2-3.1-109.9-2.6-165-2.6-55.2 0-109.9-.5-165 2.6-64 3.1-120.8 17.7-167.6 64.5C132.6 226.3 118.1 283 115 347c-3.1 55.2-2.6 109.9-2.6 165s-.5 109.9 2.6 165c3.1 64 17.7 120.8 64.5 167.6 46.9 46.9 103.6 61.4 167.6 64.5 55.2 3.1 109.9 2.6 165 2.6 55.2 0 109.9.5 165-2.6 64-3.1 120.8-17.7 167.6-64.5 46.9-46.9 61.4-103.6 64.5-167.6 3.2-55.1 2.6-109.8 2.6-165zM512 717.1c-113.5 0-205.1-91.6-205.1-205.1S398.5 306.9 512 306.9 717.1 398.5 717.1 512 625.5 717.1 512 717.1zm213.5-370.7c-26.5 0-47.9-21.4-47.9-47.9s21.4-47.9 47.9-47.9 47.9 21.4 47.9 47.9a47.84 47.84 0 0 1-47.9 47.9z"></path>--}}
        {{--                    --}}{{--                        </svg>--}}
        {{--                    --}}{{--                    </a>--}}

        {{--                    <a href="https://www.linkedin.com/company/sportlarity">--}}
        {{--                        <svg--}}
        {{--                                stroke="currentColor"--}}
        {{--                                fill="gray"--}}
        {{--                                stroke-width="0"--}}
        {{--                                viewBox="0 0 16 16"--}}
        {{--                                height="16"--}}
        {{--                                width="16"--}}
        {{--                                xmlns="http://www.w3.org/2000/svg"--}}
        {{--                        >--}}
        {{--                            <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"></path>--}}
        {{--                        </svg>--}}
        {{--                    </a>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--            <div style="background-color: #365cce; padding-top :10px; padding-bottom : 10px; color: #fff; text-align: center;">--}}
        {{--                <p class="footertext">© 2024 Sportlarity. All Rights--}}
        {{--                    Reserved.</p>--}}
        {{--            </div>--}}
        {{--        </footer>--}}
    </section>
</div>
</body>
</html>